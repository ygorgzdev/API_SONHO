<?php

namespace generic;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTHelper
{
    private static $chave_secreta = 'sua_chave_secreta_muito_segura_aqui_2024';
    private static $algoritmo = 'HS256';
    private static $tempo_expiracao = 3600; // 1 hora

    public static function gerarToken($usuario_id, $email)
    {
        try {
            $payload = [
                'iss' => 'api-sonhos', // Emissor
                'aud' => 'usuarios-sonhos', // Audiência  
                'iat' => time(), // Emitido em
                'exp' => time() + self::$tempo_expiracao, // Expira em
                'data' => [
                    'usuario_id' => $usuario_id,
                    'email' => $email
                ]
            ];

            return JWT::encode($payload, self::$chave_secreta, self::$algoritmo);
        } catch (Exception $e) {
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
            throw new Exception("Token inválido: " . $e->getMessage());
        }
    }

    public static function extrairTokenDoHeader()
    {
        try {
            $headers = getallheaders();

            if (!isset($headers['Authorization'])) {
                throw new Exception("Header Authorization não encontrado");
            }

            $authHeader = $headers['Authorization'];

            if (!preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
                throw new Exception("Formato do token inválido");
            }

            return $matches[1];
        } catch (Exception $e) {
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
            return false;
        }
    }
}
