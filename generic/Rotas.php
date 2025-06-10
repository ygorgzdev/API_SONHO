<?php

namespace generic;

//array associativo mapeando as URLs -> controllers
//crud
class Rotas
{
    private $endpoints = [];

    public function __construct()
    {
        $this->endpoints = [
            // Rotas de autenticação (não protegidas)
            "auth/login" => new Acao([
                Acao::POST => new Endpoint("Auth", "login")
            ]),
            "auth/register" => new Acao([
                Acao::POST => new Endpoint("Auth", "register")
            ]),

            // Rotas protegidas - requerem autenticação
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

    // Rotas que não precisam de autenticação
    private function rotasPublicas()
    {
        return [
            "auth/login",
            "auth/register"
        ];
    }

    //verifica se existe a rota
    //executa ação que chama controller
    //cria retorno e trata erros 
    public function executar($rota)
    {
        if (isset($this->endpoints[$rota])) {
            // Verificar se a rota precisa de autenticação
            if (!in_array($rota, $this->rotasPublicas())) {
                try {
                    $dadosUsuario = JWTHelper::verificarAutenticacao();
                    if (!$dadosUsuario) {
                        $retorno = new Retorno();
                        $retorno->erro = "Acesso não autorizado";
                        return $retorno;
                    }
                    // Disponibilizar dados do usuário para os controllers
                    $_SESSION['usuario_autenticado'] = $dadosUsuario;
                } catch (Exception $e) {
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

        //erro 404 se a rota não for encontrada
        $retorno = new Retorno();
        $retorno->erro = "Endpoint não encontrado";
        return $retorno;
    }
}
