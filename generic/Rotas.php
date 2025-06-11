<?php

namespace generic;

use Exception;

class Rotas
{
    private $endpoints = [];

    public function __construct()
    {
        $this->endpoints = [

            "auth/login" => new Acao([
                Acao::POST => new Endpoint("Auth", "login")
            ]),
            "auth/register" => new Acao([
                Acao::POST => new Endpoint("Auth", "register")
            ]),

            "auth/perfil" => new Acao([
                Acao::GET => new Endpoint("Auth", "perfil")
            ]),
            "sonhos" => new Acao([
                Acao::GET => new Endpoint("Sonho", "listar"),
                Acao::POST => new Endpoint("Sonho", "inserir")
            ]),
            "sonho" => new Acao([
                Acao::GET => new Endpoint("Sonho", "buscarPorId"),
                Acao::PUT => new Endpoint("Sonho", "atualizar"),
                Acao::DELETE => new Endpoint("Sonho", "deletar")
            ]),
            "sonhos/tag" => new Acao([
                Acao::GET => new Endpoint("Sonho", "buscarPorTag")
            ]),
            "tags" => new Acao([
                Acao::GET => new Endpoint("Tag", "listar"),
                Acao::POST => new Endpoint("Tag", "inserir")
            ]),
            "tag" => new Acao([
                Acao::GET => new Endpoint("Tag", "buscarPorId"),
                Acao::PUT => new Endpoint("Tag", "atualizar"),
                Acao::DELETE => new Endpoint("Tag", "deletar")
            ]),
            "tags/populares" => new Acao([
                Acao::GET => new Endpoint("Tag", "maisUsadas")
            ]),
            "interpretacoes" => new Acao([
                Acao::GET => new Endpoint("Interpretacao", "listar"),
                Acao::POST => new Endpoint("Interpretacao", "inserir")
            ]),
            "interpretacao" => new Acao([
                Acao::GET => new Endpoint("Interpretacao", "buscarPorId"),
                Acao::PUT => new Endpoint("Interpretacao", "atualizar"),
                Acao::DELETE => new Endpoint("Interpretacao", "deletar")
            ]),
            "sonho/interpretacoes" => new Acao([
                Acao::GET => new Endpoint("Interpretacao", "buscarPorSonho")
            ])
        ];
    }

    private function rotasPublicas()
    {
        return [
            "auth/login",
            "auth/register"
        ];
    }

    public function executar($rota)
    {
        try {
            if (isset($this->endpoints[$rota])) {
                if (!in_array($rota, $this->rotasPublicas())) {
                    try {
                        $dadosUsuario = JWTHelper::verificarAutenticacao();
                        if (!$dadosUsuario) {
                            $retorno = new Retorno();
                            $retorno->erro = "Acesso não autorizado";
                            return $retorno;
                        }

                        if (session_status() === PHP_SESSION_NONE) {
                            session_start();
                        }
                        $_SESSION['usuario_autenticado'] = $dadosUsuario;
                    } catch (Exception $e) {
                        error_log("Erro na autenticação: " . $e->getMessage());
                        $retorno = new Retorno();
                        $retorno->erro = "Acesso não autorizado";
                        return $retorno;
                    }
                }

                $endpoint = $this->endpoints[$rota];
                $dados = $endpoint->executar();

                $retorno = new Retorno();
                $retorno->dados = $dados;

                if (is_array($dados) && isset($dados['erro'])) {
                    $retorno->erro = $dados['erro'];
                    $retorno->dados = null;
                }

                return $retorno;
            }

            $retorno = new Retorno();
            $retorno->erro = "Endpoint não encontrado";
            return $retorno;
        } catch (Exception $e) {
            error_log("Erro geral no sistema de rotas: " . $e->getMessage());
            $retorno = new Retorno();
            $retorno->erro = "Erro interno do servidor";
            return $retorno;
        }
    }
}
