<?php

namespace generic;

use PDO;
use Exception;

class MysqlSingleton
{
    private static $instance = null;
    private $conexao = null;
    private $dsn = 'mysql:host=localhost;dbname=banco_sonhos';
    private $usuario = 'root';
    private $senha = '';

    private function __construct()
    {
        try {
            if ($this->conexao == null) {
                $this->conexao = new PDO($this->dsn, $this->usuario, $this->senha);
                $this->conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->conexao->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            error_log("Erro na conexão com banco de dados: " . $e->getMessage());
            throw new Exception("Falha na conexão com o banco de dados");
        }
    }

    public static function getInstance()
    {
        try {
            if (self::$instance == null) {
                self::$instance = new MysqlSingleton();
            }
            return self::$instance;
        } catch (Exception $e) {
            throw new Exception("Erro ao obter instância do banco: " . $e->getMessage());
        }
    }

    public function executar($query, $param = array())
    {
        try {
            if ($this->conexao) {
                $sth = $this->conexao->prepare($query);

                foreach ($param as $k => $v) {
                    $sth->bindValue($k, $v);
                }

                $sth->execute();
                return $sth->fetchAll(PDO::FETCH_ASSOC);
            }
            throw new Exception("Conexão com banco não estabelecida");
        } catch (Exception $e) {
            error_log("Erro na execução da query: " . $e->getMessage() . " - Query: " . $query);
            throw new Exception("Erro na execução da consulta no banco de dados");
        }
    }

    public function getLastInsertId()
    {
        try {
            if ($this->conexao) {
                return $this->conexao->lastInsertId();
            }
            throw new Exception("Conexão com banco não estabelecida");
        } catch (Exception $e) {
            error_log("Erro ao obter último ID inserido: " . $e->getMessage());
            throw new Exception("Erro ao obter ID do registro inserido");
        }
    }
}
