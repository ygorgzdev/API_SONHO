<?php

namespace service;

use dao\mysql\TagDAO;

class TagService extends TagDAO
{
    public function listarTags()
    {
        return parent::listar();
    }

    public function inserir($nome)
    {
        $nome = trim($nome);

        if (empty($nome) || strlen($nome) < 2) {
            return ["erro" => "O nome da tag deve ter pelo menos 2 caracteres"];
        }

        if (strlen($nome) > 50) {
            return ["erro" => "O nome da tag não pode ter mais de 50 caracteres"];
        }
        $tagExistente = parent::buscarPorNome($nome);
        if (!empty($tagExistente)) {
            return ["erro" => "Tag já existe"];
        }

        $tag_id = parent::inserir($nome);

        if ($tag_id) {
            return ["sucesso" => "Tag criada com sucesso!", "id" => $tag_id];
        }

        return ["erro" => "Erro ao criar a tag"];
    }

    public function listarPorId($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            return ["erro" => "ID inválido"];
        }

        return parent::listarPorId($id);
    }

    public function alterar($id, $nome)
    {
        if (!is_numeric($id) || $id <= 0) {
            return ["erro" => "ID inválido"];
        }

        $nome = trim($nome);

        if (empty($nome) || strlen($nome) < 2) {
            return ["erro" => "O nome da tag deve ter pelo menos 2 caracteres"];
        }

        if (strlen($nome) > 50) {
            return ["erro" => "O nome da tag não pode ter mais de 50 caracteres"];
        }

        $tagAtual = parent::listarPorId($id);
        if (empty($tagAtual)) {
            return ["erro" => "Tag não encontrada"];
        }

        $tagExistente = parent::buscarPorNome($nome);
        if (!empty($tagExistente) && $tagExistente[0]['id'] != $id) {
            return ["erro" => "Já existe uma tag com esse nome"];
        }

        $resultado = parent::alterar($id, $nome);

        if ($resultado !== false && $resultado !== null) {
            return ["sucesso" => "Tag atualizada com sucesso!"];
        }

        return ["erro" => "Erro ao atualizar a tag"];
    }

    public function deletar($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            return ["erro" => "ID inválido"];
        }

        $tagExistente = parent::listarPorId($id);
        if (empty($tagExistente)) {
            return ["erro" => "Tag não encontrada"];
        }

        $resultado = parent::deletar($id);
        if ($resultado !== false && $resultado !== null) {
            return ["sucesso" => "Tag deletada com sucesso!"];
        }
        return ["erro" => "Erro ao deletar a tag"];
    }

    public function tagsMaisUsadas()
    {
        return parent::tagsMaisUsadas();
    }
}
