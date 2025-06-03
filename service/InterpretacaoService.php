<?php

namespace service;

use dao\mysql\InterpretacaoDAO;
use dao\mysql\SonhoDAO;

class InterpretacaoService extends InterpretacaoDAO
{
    private SonhoDAO $sonhoDAO;

    public function __construct()
    {
        parent::__construct();
        $this->sonhoDAO = new SonhoDAO();
    }

    public function listarInterpretacoes()
    {
        return parent::listar();
    }

    public function inserir($sonho_id, $interpretador, $texto)
    {
        if (!is_numeric($sonho_id) || $sonho_id <= 0) {
            return ["erro" => "ID do sonho inválido"];
        }
        $sonho = $this->sonhoDAO->listarPorId($sonho_id);
        if (empty($sonho)) {
            return ["erro" => "Sonho não encontrado"];
        }

        $interpretador = trim($interpretador);
        $texto = trim($texto);

        if (empty($interpretador) || strlen($interpretador) < 2) {
            return ["erro" => "Nome do interpretador deve ter pelo menos 2 caracteres"];
        }

        if (strlen($interpretador) > 100) {
            return ["erro" => "Nome do interpretador não pode ter mais de 100 caracteres"];
        }

        if (empty($texto) || strlen($texto) < 20) {
            return ["erro" => "Interpretação deve ter pelo menos 20 caracteres"];
        }

        $interpretacao_id = parent::inserir($sonho_id, $interpretador, $texto);

        if ($interpretacao_id) {
            return ["sucesso" => "Interpretação salva com sucesso!", "id" => $interpretacao_id];
        }

        return ["erro" => "Erro ao salvar a interpretação"];
    }

    public function listarPorId($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            return ["erro" => "ID inválido"];
        }

        return parent::listarPorId($id);
    }

    public function listarPorSonho($sonho_id)
    {
        if (!is_numeric($sonho_id) || $sonho_id <= 0) {
            return ["erro" => "ID do sonho inválido"];
        }
        $sonho = $this->sonhoDAO->listarPorId($sonho_id);
        if (empty($sonho)) {
            return ["erro" => "Sonho não encontrado"];
        }

        return parent::listarPorSonho($sonho_id);
    }

    public function alterar($id, $interpretador, $texto)
    {
        if (!is_numeric($id) || $id <= 0) {
            return ["erro" => "ID inválido"];
        }
        $interpretacaoExistente = parent::listarPorId($id);
        if (empty($interpretacaoExistente)) {
            return ["erro" => "Interpretação não encontrada"];
        }

        $interpretador = trim($interpretador);
        $texto = trim($texto);

        if (empty($interpretador) || strlen($interpretador) < 2) {
            return ["erro" => "Nome do interpretador deve ter pelo menos 2 caracteres"];
        }

        if (strlen($interpretador) > 100) {
            return ["erro" => "Nome do interpretador não pode ter mais de 100 caracteres"];
        }

        if (empty($texto) || strlen($texto) < 20) {
            return ["erro" => "Interpretação deve ter pelo menos 20 caracteres"];
        }

        $resultado = parent::alterar($id, $interpretador, $texto);
        if ($resultado !== false && $resultado !== null) {
            return ["sucesso" => "Interpretação atualizada com sucesso!"];
        }

        return ["erro" => "Erro ao atualizar a interpretação"];
    }

    public function deletar($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            return ["erro" => "ID inválido"];
        }
        $interpretacaoExistente = parent::listarPorId($id);
        if (empty($interpretacaoExistente)) {
            return ["erro" => "Interpretação não encontrada"];
        }
        $resultado = parent::deletar($id);

        if ($resultado !== false && $resultado !== null) {
            return ["sucesso" => "Interpretação deletada com sucesso!"];
        }

        return ["erro" => "Erro ao deletar a interpretação"];
    }
}
