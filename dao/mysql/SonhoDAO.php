<?php

namespace dao\mysql;

use dao\ISonhoDAO;
use generic\MysqlFactory;

//comunica com bd

class SonhoDAO extends MysqlFactory implements ISonhoDAO
{
    public function listar()
    {
        $sql = "SELECT s.id, s.conteudo, s.criado_em, 
                GROUP_CONCAT(t.nome SEPARATOR ', ') as tags
                FROM sonhos s 
                LEFT JOIN sonho_tag st ON s.id = st.sonho_id 
                LEFT JOIN tags t ON st.tag_id = t.id 
                GROUP BY s.id 
                ORDER BY s.criado_em DESC";
        $retorno = $this->banco->executar($sql); //this - mysqlfactory
        return $retorno;
    }

    public function inserir($conteudo)
    {
        $sql = "INSERT INTO sonhos (conteudo) VALUES (:conteudo)";
        $param = [
            ":conteudo" => $conteudo
        ];
        $this->banco->executar($sql, $param);
        return $this->banco->getLastInsertId();
    }

    public function listarPorId($id)
    {
        $sql = "SELECT s.id, s.conteudo, s.criado_em, 
                GROUP_CONCAT(t.nome SEPARATOR ', ') as tags
                FROM sonhos s 
                LEFT JOIN sonho_tag st ON s.id = st.sonho_id 
                LEFT JOIN tags t ON st.tag_id = t.id 
                WHERE s.id = :id 
                GROUP BY s.id";
        $param = [
            ":id" => $id
        ];
        $retorno = $this->banco->executar($sql, $param);
        return $retorno;
    }

    public function alterar($id, $conteudo)
    {
        $sql = "UPDATE sonhos SET conteudo = :conteudo WHERE id = :id";
        $param = [
            ":id" => $id,
            ":conteudo" => $conteudo
        ];
        $retorno = $this->banco->executar($sql, $param);
        return $retorno;
    }

    public function deletar($id)
    {
        $sql = "DELETE FROM sonhos WHERE id = :id";
        $param = [
            ":id" => $id
        ];
        $retorno = $this->banco->executar($sql, $param);
        return $retorno;
    }

    public function buscarPorTag($tag)
    {
        $sql = "SELECT DISTINCT s.id, s.conteudo, s.criado_em, 
                GROUP_CONCAT(t2.nome SEPARATOR ', ') as tags
                FROM sonhos s 
                JOIN sonho_tag st ON s.id = st.sonho_id 
                JOIN tags t ON st.tag_id = t.id 
                LEFT JOIN sonho_tag st2 ON s.id = st2.sonho_id 
                LEFT JOIN tags t2 ON st2.tag_id = t2.id 
                WHERE t.nome LIKE :tag 
                GROUP BY s.id 
                ORDER BY s.criado_em DESC";
        $param = [
            ":tag" => "%$tag%"
        ];
        $retorno = $this->banco->executar($sql, $param);
        return $retorno;
    }

    public function associarTag($sonho_id, $tag_id)
    {
        $sql = "INSERT IGNORE INTO sonho_tag (sonho_id, tag_id) VALUES (:sonho_id, :tag_id)";
        $param = [
            ":sonho_id" => $sonho_id,
            ":tag_id" => $tag_id
        ];
        $retorno = $this->banco->executar($sql, $param);
        return $retorno;
    }

    public function removerTodasTags($sonho_id)
    {
        $sql = "DELETE FROM sonho_tag WHERE sonho_id = :sonho_id";
        $param = [
            ":sonho_id" => $sonho_id
        ];
        $retorno = $this->banco->executar($sql, $param);
        return $retorno;
    }
}
