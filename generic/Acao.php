<?php

namespace generic;

use ReflectionMethod;

//reflection para chamar métodos dinamicamente

class Acao
{

    const POST = "POST";
    const GET = "GET";
    const PUT = "PUT";
    const PATCH = "PATCH";
    const DELETE = "DELETE";

    private $endpoint;

    public function __construct($endpoint = [])
    {

        $this->endpoint = $endpoint;
    }

    //pega endpoint baseado no metodo http 
    public function executar()
    {
        $end = $this->endpointMetodo();

        //reflection pra examinar metodo do controller
        if ($end) {
            $reflectMetodo = new ReflectionMethod($end->classe, $end->execucao);
            $parametros = $reflectMetodo->getParameters(); //parametros que o metodo espera
            $returnParam = $this->getParam(); //pega todos parametros disponiveis
            if ($parametros) {
                $para = [];
                //monta parametros na ordem correta (metodo controller)
                foreach ($parametros as $v) {
                    $name = $v->getName();

                    if (!isset($returnParam[$name])) {
                        return false;
                    }
                    $para[$name] = $returnParam[$name];
                }
                //pega param passado pelo endpoint
                return $reflectMetodo->invokeArgs(new $end->classe(), $para);
            }
            return $reflectMetodo->invoke(new $end->classe());
        }
        return null;
    }

    private function endpointMetodo()
    {
        return isset($this->endpoint[$_SERVER["REQUEST_METHOD"]]) ? $this->endpoint[$_SERVER["REQUEST_METHOD"]] : null;
    }

    private function getPost() //pega dados do formulario
    {
        if ($_POST) {
            return $_POST;
        }
        return [];
    }
    private function getGet() // pega param da url 
    {
        if ($_GET) {
            $get = $_GET;
            unset($get["param"]);
            return $get;
        }
        return [];
    }
    private function getInput() //pega json do corpo da requisição
    {
        $input = file_get_contents("php://input");

        if ($input) {

            return json_decode($input, true);
        }
        return [];
    }


    public function getParam()
    {
        return array_merge($this->getPost(), $this->getGet(), $this->getInput()); //junta tudo, prioridade pra json
    }
}


//factory