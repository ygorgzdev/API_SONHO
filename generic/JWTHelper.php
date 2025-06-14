<?php

namespace generic;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class JWTHelper
{
    private static $chave_secreta = 'uY^#4b3Lz9rVm7!D@wXqTk&E2jHsPpC8Z0Ng%a1Q';
    private static $algoritmo = 'HS256';
    private static $tempo_expiracao = 3600;

    public static function gerarToken($usuario_id, $email)
    {
        try {
            $payload = [
                'iss' => 'api-sonhos',
                'aud' => 'usuarios-sonhos',
                'iat' => time(),
                'exp' => time() + self::$tempo_expiracao,
                'data' => [
                    'usuario_id' => $usuario_id,
                    'email' => $email
                ]
            ];

            return JWT::encode($payload, self::$chave_secreta, self::$algoritmo);
        } catch (Exception $e) {
            error_log("Erro ao gerar token JWT: " . $e->getMessage());
            throw new Exception("Erro ao gerar token: " . $e->getMessage());
        }
    }

    public static function validarToken($token)
    {
        try {
            if (empty($token)) {
                throw new Exception("Token não fornecido");
            }

            $decoded = JWT::decode($token, new Key(self::$chave_secreta, self::$algoritmo));
            return $decoded->data;
        } catch (Exception $e) {
            error_log("Erro ao validar token JWT: " . $e->getMessage());
            throw new Exception("Token inválido: " . $e->getMessage());
        }
    }

    public static function extrairTokenDoHeader()
    {
        try {
            $headers = getallheaders();

            if (!$headers || !isset($headers['Authorization'])) {
                throw new Exception("Header Authorization não encontrado");
            }

            $authHeader = $headers['Authorization'];

            if (!preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
                throw new Exception("Formato do token inválido");
            }

            return $matches[1];
        } catch (Exception $e) {
            error_log("Erro ao extrair token do header: " . $e->getMessage());
            throw new Exception("Erro ao extrair token: " . $e->getMessage());
        }
    }

    public static function verificarAutenticacao()
    {
        try {
            $token = self::extrairTokenDoHeader();
            $dadosUsuario = self::validarToken($token);
            return $dadosUsuario;
        } catch (Exception $e) {
            error_log("Falha na verificação de autenticação: " . $e->getMessage());
            return false;
        }
    }
}
