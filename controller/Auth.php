<?php

namespace controller;

use service\AuthService;
use Exception;

class Auth
{
    public function login($email, $senha)
    {
        try {
            // Validação básica dos parâmetros
            if (empty($email) || empty($senha)) {
                return ["erro" => "Email e senha são obrigatórios"];
            }

            $service = new AuthService();
            $resultado = $service->login($email, $senha);
            return $resultado;
        } catch (Exception $e) {
            error_log("Erro no controller de login: " . $e->getMessage());
            return ["erro" => "Erro interno no sistema de autenticação"];
        }
    }

    public function register($email, $senha, $nome)
    {
        try {
            // Validação básica dos parâmetros
            if (empty($email) || empty($senha) || empty($nome)) {
                return ["erro" => "Email, senha e nome são obrigatórios"];
            }

            $service = new AuthService();
            $resultado = $service->registrar($email, $senha, $nome);
            return $resultado;
        } catch (Exception $e) {
            error_log("Erro no controller de registro: " . $e->getMessage());
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
            error_log("Erro no controller de perfil: " . $e->getMessage());
            return ["erro" => "Erro ao obter perfil do usuário"];
        }
    }
}
