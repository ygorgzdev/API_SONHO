<?php

namespace controller;

use service\AuthService;

class Auth
{
    public function login($email, $senha)
    {
        try {
            $service = new AuthService();
            $resultado = $service->login($email, $senha);
            return $resultado;
        } catch (Exception $e) {
            return ["erro" => "Erro interno no sistema de autenticação"];
        }
    }

    public function register($email, $senha, $nome)
    {
        try {
            $service = new AuthService();
            $resultado = $service->registrar($email, $senha, $nome);
            return $resultado;
        } catch (Exception $e) {
            return ["erro" => "Erro interno no sistema de registro"];
        }
    }

    public function perfil()
    {
        try {
            $service = new AuthService();
            $resultado = $service->obterPerfil();
            return $resultado;
        } catch (Exception $e) {
            return ["erro" => "Erro ao obter perfil do usuário"];
        }
    }
}
