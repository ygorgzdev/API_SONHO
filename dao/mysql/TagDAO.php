<?php

namespace dao\mysql;

use dao\ITagDAO;
use generic\MysqlFactory;

class TagDAO extends MysqlFactory implements ITagDAO
{
    public function listar()
    {
        $sql = "SELECT id, nome FROM tags ORDER BY nome";
        $retorno = $this->banco->executar($sql);
        return $retorno;
    }

    public function inserir($nome)
    {
        $sql = "INSERT INTO tags (nome) VALUES (:nome)";
        $param = [
            ":nome" => $nome
        ];
        $this->banco->executar($sql, $param);
        return $this->banco->getLastInsertId();
    }

    public function listarPorId($id)
    {
        $sql = "SELECT id, nome FROM tags WHERE id = :id";
        $param = [
            ":id" => $id
        ];
        $retorno = $this->banco->executar($sql, $param);
        return $retorno;
    }

    public function buscarPorNome($nome)
    {
        $sql = "SELECT id, nome FROM tags WHERE nome = :nome";
        $param = [
            ":nome" => $nome
        ];
        $retorno = $this->banco->executar($sql, $param);
        return $retorno;
    }

    public function alterar($id, $nome)
    {
        $sql = "UPDATE tags SET nome = :nome WHERE id = :id";
        $param = [
            ":id" => $id,
            ":nome" => $nome
        ];
        $retorno = $this->banco->executar($sql, $param);
        return $retorno;
    }

    public function deletar($id)
    {
        $sql = "DELETE FROM tags WHERE id = :id";
        $param = [
            ":id" => $id
        ];
        $retorno = $this->banco->executar($sql, $param);
        return $retorno;
    }

    public function tagsMaisUsadas()
    {
        $sql = "SELECT t.id, t.nome, COUNT(st.tag_id) as total_uso 
                FROM tags t 
                LEFT JOIN sonho_tag st ON t.id = st.tag_id 
                GROUP BY t.id, t.nome 
                ORDER BY total_uso DESC 
                LIMIT 10";
        $retorno = $this->banco->executar($sql);
        return $retorno;
    }
}
