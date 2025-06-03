<?php

namespace generic;

//instacia de rotas 
//generic/rotas executa a rota
//todas resposta = json
//logica codigos http
//converta json e envia

class Controller
{
    private $rotas = null;
    public function __construct()
    {
        $this->rotas = new Rotas();
    }

    public function verificarChamadas($rota)
    {
        $retorno = $this->rotas->executar($rota);
        if ($retorno) {
            header("Content-Type: application/json");

            if (isset($retorno->erro) && $retorno->erro !== null) {
                if ($retorno->erro === "Endpoint não encontrado") {
                    http_response_code(404);
                } elseif (strpos($retorno->erro, "não encontrado") !== false) {
                    http_response_code(404);
                } elseif (
                    strpos($retorno->erro, "inválido") !== false ||
                    strpos($retorno->erro, "deve ter") !== false ||
                    strpos($retorno->erro, "já existe") !== false
                ) {
                    http_response_code(400);
                } else {
                    http_response_code(500);
                }
            } else {
                if (
                    $_SERVER['REQUEST_METHOD'] === 'POST' &&
                    isset($retorno->dados) &&
                    is_array($retorno->dados) &&
                    isset($retorno->dados['sucesso'])
                ) {
                    http_response_code(201); //cria
                } else {
                    http_response_code(200); //ok
                }
            }

            $json = json_encode($retorno);
            echo $json;
        } else {
            http_response_code(500); //sem retorno
            echo json_encode(["erro" => "Erro interno do servidor"]);
        }
    }
}


//retorno