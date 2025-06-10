<?php

namespace service;

use dao\mysql\UsuarioDAO;
use generic\JWTHelper;
use Exception;

class AuthService
{
    private UsuarioDAO $usuarioDAO;

    public function __construct()
    {
        $this->usuarioDAO = new UsuarioDAO();
    }

    public function login($email, $senha)
    {
        try {
            // Validação dos dados de entrada
            if (empty($email) || empty($senha)) {
                return ["erro" => "Email e senha são obrigatórios"];
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return ["erro" => "Email inválido"];
            }

            // Buscar usuário no banco
            $usuario = $this->usuarioDAO->buscarPorEmail($email);

            if (empty($usuario)) {
                return ["erro" => "Credenciais inválidas"];
            }

            $usuarioData = $usuario[0];

            // Verificar senha
            if (!password_verify($senha, $usuarioData['senha'])) {
                return ["erro" => "Credenciais inválidas"];
            }

            // Gerar token JWT
            $token = JWTHelper::gerarToken($usuarioData['id'], $usuarioData['email']);

            return [
                "sucesso" => "Login realizado com sucesso",
                "token" => $token,
                "usuario" => [
                    "id" => $usuarioData['id'],
                    "email" => $usuarioData['email'],
                    "nome" => $usuarioData['nome']
                ]
            ];
        } catch (Exception $e) {
            error_log("Erro no login: " . $e->getMessage());
            return ["erro" => "Erro interno no sistema de login"];
        }
    }

    public function registrar($email, $senha, $nome)
    {
        try {
            // Validações
            if (empty($email) || empty($senha) || empty($nome)) {
                return ["erro" => "Email, senha e nome são obrigatórios"];
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return ["erro" => "Email inválido"];
            }

            if (strlen($senha) < 6) {
                return ["erro" => "Senha deve ter pelo menos 6 caracteres"];
            }

            if (strlen($nome) < 2) {
                return ["erro" => "Nome deve ter pelo menos 2 caracteres"];
            }

            // Verificar se email já existe
            $usuarioExistente = $this->usuarioDAO->buscarPorEmail($email);
            if (!empty($usuarioExistente)) {
                return ["erro" => "Email já está em uso"];
            }

            // Hash da senha
            $senhaHash = password_hash($senha, PASSWORD_BCRYPT);

            // Inserir usuário
            $usuario_id = $this->usuarioDAO->inserir($email, $senhaHash, $nome);

            if ($usuario_id) {
                return ["sucesso" => "Usuário registrado com sucesso!", "id" => $usuario_id];
            }

            return ["erro" => "Erro ao registrar usuário"];
        } catch (Exception $e) {
            error_log("Erro no registro: " . $e->getMessage());
            return ["erro" => "Erro interno no sistema de registro"];
        }
    }

    public function obterPerfil()
    {
        try {
            $dadosUsuario = JWTHelper::verificarAutenticacao();

            if (!$dadosUsuario) {
                return ["erro" => "Acesso não autorizado"];
            }

            $usuario = $this->usuarioDAO->listarPorId($dadosUsuario->usuario_id);

            if (empty($usuario)) {
                return ["erro" => "Usuário não encontrado"];
            }

            return $usuario[0];
        } catch (Exception $e) {
            error_log("Erro ao obter perfil: " . $e->getMessage());
            return ["erro" => "Erro interno ao obter perfil"];
        }
    }
}
