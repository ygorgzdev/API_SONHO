<?php

namespace dao\mysql;

use dao\IUsuarioDAO;
use generic\MysqlFactory;
use Exception;

class UsuarioDAO extends MysqlFactory implements IUsuarioDAO
{
    public function buscarPorEmail($email)
    {
        try {
            $sql = "SELECT id, email, senha, nome, criado_em FROM usuarios WHERE email = :email";
            $param = [
                ":email" => $email
            ];
            $retorno = $this->banco->executar($sql, $param);
            return $retorno;
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar usuário por email: " . $e->getMessage());
        }
    }

    public function inserir($email, $senha, $nome)
    {
        try {
            $sql = "INSERT INTO usuarios (email, senha, nome) VALUES (:email, :senha, :nome)";
            $param = [
                ":email" => $email,
                ":senha" => $senha,
                ":nome" => $nome
            ];
            $this->banco->executar($sql, $param);
            return $this->banco->getLastInsertId();
        } catch (Exception $e) {
            throw new Exception("Erro ao inserir usuário: " . $e->getMessage());
        }
    }

    public function listarPorId($id)
    {
        try {
            $sql = "SELECT id, email, nome, criado_em FROM usuarios WHERE id = :id";
            $param = [
                ":id" => $id
            ];
            $retorno = $this->banco->executar($sql, $param);
            return $retorno;
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar usuário por ID: " . $e->getMessage());
        }
    }
}
