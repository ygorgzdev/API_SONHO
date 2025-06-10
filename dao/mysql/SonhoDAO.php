<?php

namespace dao\mysql;

use dao\ISonhoDAO;
use generic\MysqlFactory;
use Exception;

class SonhoDAO extends MysqlFactory implements ISonhoDAO
{
    public function listar()
    {
        try {
            $sql = "SELECT s.id, s.conteudo, s.criado_em, 
                    GROUP_CONCAT(t.nome SEPARATOR ', ') as tags
                    FROM sonhos s 
                    LEFT JOIN sonho_tag st ON s.id = st.sonho_id 
                    LEFT JOIN tags t ON st.tag_id = t.id 
                    GROUP BY s.id 
                    ORDER BY s.criado_em DESC";
            $retorno = $this->banco->executar($sql);
            return $retorno;
        } catch (Exception $e) {
            error_log("Erro ao listar sonhos no DAO: " . $e->getMessage());
            throw new Exception("Erro na consulta de sonhos");
        }
    }

    public function inserir($conteudo)
    {
        try {
            $sql = "INSERT INTO sonhos (conteudo) VALUES (:conteudo)";
            $param = [
                ":conteudo" => $conteudo
            ];
            $this->banco->executar($sql, $param);
            return $this->banco->getLastInsertId();
        } catch (Exception $e) {
            error_log("Erro ao inserir sonho no DAO: " . $e->getMessage());
            throw new Exception("Erro ao inserir novo sonho");
        }
    }

    public function listarPorId($id)
    {
        try {
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
        } catch (Exception $e) {
            error_log("Erro ao buscar sonho por ID no DAO: " . $e->getMessage());
            throw new Exception("Erro ao buscar sonho específico");
        }
    }

    public function alterar($id, $conteudo)
    {
        try {
            $sql = "UPDATE sonhos SET conteudo = :conteudo WHERE id = :id";
            $param = [
                ":id" => $id,
                ":conteudo" => $conteudo
            ];
            $retorno = $this->banco->executar($sql, $param);
            return $retorno;
        } catch (Exception $e) {
            error_log("Erro ao alterar sonho no DAO: " . $e->getMessage());
            throw new Exception("Erro ao atualizar sonho");
        }
    }

    public function deletar($id)
    {
        try {
            $sql = "DELETE FROM sonhos WHERE id = :id";
            $param = [
                ":id" => $id
            ];
            $retorno = $this->banco->executar($sql, $param);
            return $retorno;
        } catch (Exception $e) {
            error_log("Erro ao deletar sonho no DAO: " . $e->getMessage());
            throw new Exception("Erro ao deletar sonho");
        }
    }

    public function buscarPorTag($tag)
    {
        try {
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
        } catch (Exception $e) {
            error_log("Erro ao buscar sonhos por tag no DAO: " . $e->getMessage());
            throw new Exception("Erro ao buscar sonhos por tag");
        }
    }

    public function associarTag($sonho_id, $tag_id)
    {
        try {
            $sql = "INSERT IGNORE INTO sonho_tag (sonho_id, tag_id) VALUES (:sonho_id, :tag_id)";
            $param = [
                ":sonho_id" => $sonho_id,
                ":tag_id" => $tag_id
            ];
            $retorno = $this->banco->executar($sql, $param);
            return $retorno;
        } catch (Exception $e) {
            error_log("Erro ao associar tag ao sonho no DAO: " . $e->getMessage());
            throw new Exception("Erro ao associar tag");
        }
    }

    public function removerTodasTags($sonho_id)
    {
        try {
            $sql = "DELETE FROM sonho_tag WHERE sonho_id = :sonho_id";
            $param = [
                ":sonho_id" => $sonho_id
            ];
            $retorno = $this->banco->executar($sql, $param);
            return $retorno;
        } catch (Exception $e) {
            error_log("Erro ao remover tags do sonho no DAO: " . $e->getMessage());
            throw new Exception("Erro ao remover tags do sonho");
        }
    }
}
