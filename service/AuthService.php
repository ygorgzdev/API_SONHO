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
        try {
            $this->usuarioDAO = new UsuarioDAO();
        } catch (Exception $e) {
            error_log("Erro ao inicializar AuthService: " . $e->getMessage());
            throw new Exception("Erro interno do sistema");
        }
    }

    public function login($email, $senha)
    {
        try {
            // Validação dos dados de entrada
            if (empty($email) || empty($senha)) {
                return ["erro" => "Email e senha são obrigatórios"];
            }

            $email = trim($email);
            $senha = trim($senha);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return ["erro" => "Email inválido"];
            }

            if (strlen($senha) < 3) {
                return ["erro" => "Senha deve ter pelo menos 3 caracteres"];
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
            // Validações de entrada
            if (empty($email) || empty($senha) || empty($nome)) {
                return ["erro" => "Email, senha e nome são obrigatórios"];
            }

            $email = trim($email);
            $senha = trim($senha);
            $nome = trim($nome);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return ["erro" => "Email inválido"];
            }

            if (strlen($senha) < 6) {
                return ["erro" => "Senha deve ter pelo menos 6 caracteres"];
            }

            if (strlen($nome) < 2) {
                return ["erro" => "Nome deve ter pelo menos 2 caracteres"];
            }

            if (strlen($nome) > 100) {
                return ["erro" => "Nome não pode ter mais de 100 caracteres"];
            }

            // Verificar se email já existe
            $usuarioExistente = $this->usuarioDAO->buscarPorEmail($email);
            if (!empty($usuarioExistente)) {
                return ["erro" => "Email já está em uso"];
            }

            // Hash da senha
            $senhaHash = password_hash($senha, PASSWORD_BCRYPT, ['cost' => 12]);

            if (!$senhaHash) {
                throw new Exception("Erro ao processar senha");
            }

            // Inserir usuário
            $usuario_id = $this->usuarioDAO->inserir($email, $senhaHash, $nome);

            if ($usuario_id) {
                return [
                    "sucesso" => "Usuário registrado com sucesso!",
                    "id" => $usuario_id,
                    "email" => $email,
                    "nome" => $nome
                ];
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

            if (!isset($dadosUsuario->usuario_id) || !is_numeric($dadosUsuario->usuario_id)) {
                return ["erro" => "Token inválido"];
            }

            $usuario = $this->usuarioDAO->listarPorId($dadosUsuario->usuario_id);

            if (empty($usuario)) {
                return ["erro" => "Usuário não encontrado"];
            }

            $dadosUsuario = $usuario[0];

            // Remover dados sensíveis antes de retornar
            unset($dadosUsuario['senha']);

            return [
                "sucesso" => "Perfil obtido com sucesso",
                "usuario" => $dadosUsuario
            ];
        } catch (Exception $e) {
            error_log("Erro ao obter perfil: " . $e->getMessage());
            return ["erro" => "Erro interno ao obter perfil"];
        }
    }
}
