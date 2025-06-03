<?php

namespace generic;


//define qual controller e metodo vai executar
//adiciona namespace controller
class Endpoint
{
    public $classe; //nome classe ex: controller\\sonho
    public $execucao; //nome metodo ex: listar

    public function __construct($classe, $execucao)
    {
        $this->classe = "controller\\" . $classe; //add namespace controller)
        $this->execucao = $execucao;
    }
}


//acao