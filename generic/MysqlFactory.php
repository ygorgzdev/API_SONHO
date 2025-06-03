<?php

namespace generic;

//fornece acesso a conexão


class MysqlFactory
{
    public MysqlSingleton $banco; //prop publi tipo mysqlsingleton
    public function __construct()
    {
        $this->banco = MysqlSingleton::getInstance(); //pega instancia unica do banco
    }
}

//abstração;flexibilidade;herança

//singleton