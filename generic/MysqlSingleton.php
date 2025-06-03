<?php

namespace generic;

use PDO;

class MysqlSingleton
{
    private static $instance = null;
    private $conexao = null;
    private $dsn = 'mysql:host=localhost;dbname=banco_sonhos';
    private $usuario = 'root';
    private $senha = '';

    private function __construct()
    {
        if ($this->conexao == null) {
            $this->conexao = new PDO($this->dsn, $this->usuario, $this->senha); //pdo com tratamento de exceção
            $this->conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
    }

    public static function getInstance() //retorna sempre a mesma instacia
    {
        if (self::$instance == null) {
            self::$instance = new MysqlSingleton();
        }
        return self::$instance;
    }

    public function executar($query, $param = array()) //sql com instrução preparada (repetid, efici, no injection sql)
    {
        if ($this->conexao) {
            $sth = $this->conexao->prepare($query);
            foreach ($param as $k => $v) {
                $sth->bindValue($k, $v);
            }
            $sth->execute();
            return $sth->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    public function getLastInsertId() //id do ultimo registro
    {
        return $this->conexao->lastInsertId();
    }
}


//controllers
