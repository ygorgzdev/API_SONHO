<?php

namespace dao\mysql;

use dao\IInterpretacaoDAO;
use generic\MysqlFactory;

class InterpretacaoDAO extends MysqlFactory implements IInterpretacaoDAO
{
    public function listar()
    {
        $sql = "SELECT i.id, i.sonho_id, i.interpretador, i.texto, i.criado_em,
                s.conteudo as sonho_conteudo
                FROM interpretacoes i
                JOIN sonhos s ON i.sonho_id = s.id
                ORDER BY i.criado_em DESC";
        $retorno = $this->banco->executar($sql);
        return $retorno;
    }

    public function inserir($sonho_id, $interpretador, $texto)
    {
        $sql = "INSERT INTO interpretacoes (sonho_id, interpretador, texto) 
                VALUES (:sonho_id, :interpretador, :texto)";
        $param = [
            ":sonho_id" => $sonho_id,
            ":interpretador" => $interpretador,
            ":texto" => $texto
        ];
        $this->banco->executar($sql, $param);
        return $this->banco->getLastInsertId();
    }

    public function listarPorId($id)
    {
        $sql = "SELECT i.id, i.sonho_id, i.interpretador, i.texto, i.criado_em,
                s.conteudo as sonho_conteudo
                FROM interpretacoes i
                JOIN sonhos s ON i.sonho_id = s.id
                WHERE i.id = :id";
        $param = [
            ":id" => $id
        ];
        $retorno = $this->banco->executar($sql, $param);
        return $retorno;
    }

    public function listarPorSonho($sonho_id)
    {
        $sql = "SELECT id, sonho_id, interpretador, texto, criado_em
                FROM interpretacoes
                WHERE sonho_id = :sonho_id
                ORDER BY criado_em DESC";
        $param = [
            ":sonho_id" => $sonho_id
        ];
        $retorno = $this->banco->executar($sql, $param);
        return $retorno;
    }

    public function alterar($id, $interpretador, $texto)
    {
        $sql = "UPDATE interpretacoes 
                SET interpretador = :interpretador, texto = :texto 
                WHERE id = :id";
        $param = [
            ":id" => $id,
            ":interpretador" => $interpretador,
            ":texto" => $texto
        ];
        $retorno = $this->banco->executar($sql, $param);
        return $retorno;
    }

    public function deletar($id)
    {
        $sql = "DELETE FROM interpretacoes WHERE id = :id";
        $param = [
            ":id" => $id
        ];
        $retorno = $this->banco->executar($sql, $param);
        return $retorno;
    }
}
