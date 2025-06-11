<?php

namespace service;

use dao\mysql\SonhoDAO;
use dao\mysql\TagDAO;
use generic\JWTHelper;
use Exception;

class SonhoService extends SonhoDAO
{
    private TagDAO $tagDAO;

    public function __construct()
    {
        try {
            parent::__construct();
            $this->tagDAO = new TagDAO();
        } catch (Exception $e) {
            error_log("Erro ao inicializar SonhoService: " . $e->getMessage());
            throw new Exception("Erro interno no serviço de sonhos");
        }
    }

    public function listarSonhos()
    {
        try {
            // Verificar autenticação
            $dadosUsuario = JWTHelper::verificarAutenticacao();
            if (!$dadosUsuario) {
                return ["erro" => "Acesso não autorizado"];
            }

            return parent::listar();
        } catch (Exception $e) {
            error_log("Erro ao listar sonhos: " . $e->getMessage());
            return ["erro" => "Erro interno ao listar sonhos"];
        }
    }

    public function inserir($conteudo, $tags = [])
    {
        try {
            // Verificar autenticação
            $dadosUsuario = JWTHelper::verificarAutenticacao();
            if (!$dadosUsuario) {
                return ["erro" => "Acesso não autorizado"];
            }

            // Validações
            if (empty($conteudo) || strlen(trim($conteudo)) < 10) {
                return ["erro" => "O conteúdo do sonho deve ter pelo menos 10 caracteres"];
            }

            $conteudo = trim($conteudo);
            $sonho_id = parent::inserir($conteudo);

            if ($sonho_id && !empty($tags)) {
                $this->processarTags($sonho_id, $tags);
            }

            if ($sonho_id) {
                return ["sucesso" => "Sonho salvo com sucesso!", "id" => $sonho_id];
            }

            return ["erro" => "Erro ao salvar o sonho"];
        } catch (Exception $e) {
            error_log("Erro ao inserir sonho: " . $e->getMessage());
            return ["erro" => "Erro interno ao salvar sonho"];
        }
    }

    public function listarPorId($id)
    {
        try {
            // Verificar autenticação
            $dadosUsuario = JWTHelper::verificarAutenticacao();
            if (!$dadosUsuario) {
                return ["erro" => "Acesso não autorizado"];
            }

            if (!is_numeric($id) || $id <= 0) {
                return ["erro" => "ID inválido"];
            }

            return parent::listarPorId($id);
        } catch (Exception $e) {
            error_log("Erro ao buscar sonho por ID: " . $e->getMessage());
            return ["erro" => "Erro interno ao buscar sonho"];
        }
    }

    public function alterar($id, $conteudo, $tags = [])
    {
        try {
            // Verificar autenticação
            $dadosUsuario = JWTHelper::verificarAutenticacao();
            if (!$dadosUsuario) {
                return ["erro" => "Acesso não autorizado"];
            }

            if (!is_numeric($id) || $id <= 0) {
                return ["erro" => "ID inválido"];
            }

            if (empty($conteudo) || strlen(trim($conteudo)) < 10) {
                return ["erro" => "O conteúdo do sonho deve ter pelo menos 10 caracteres"];
            }

            // Verificar se o sonho existe
            $sonhoExistente = parent::listarPorId($id);
            if (empty($sonhoExistente)) {
                return ["erro" => "Sonho não encontrado"];
            }

            $conteudo = trim($conteudo);
            $resultado = parent::alterar($id, $conteudo);

            if ($resultado !== false && $resultado !== null) {
                // Remover tags antigas e adicionar novas
                parent::removerTodasTags($id);
                if (!empty($tags)) {
                    $this->processarTags($id, $tags);
                }
                return ["sucesso" => "Sonho atualizado com sucesso!"];
            }

            return ["erro" => "Erro ao atualizar o sonho"];
        } catch (Exception $e) {
            error_log("Erro ao atualizar sonho: " . $e->getMessage());
            return ["erro" => "Erro interno ao atualizar sonho"];
        }
    }

    public function deletar($id)
    {
        try {
            // Verificar autenticação
            $dadosUsuario = JWTHelper::verificarAutenticacao();
            if (!$dadosUsuario) {
                return ["erro" => "Acesso não autorizado"];
            }

            if (!is_numeric($id) || $id <= 0) {
                return ["erro" => "ID inválido"];
            }

            $sonhoExistente = parent::listarPorId($id);
            if (empty($sonhoExistente)) {
                return ["erro" => "Sonho não encontrado"];
            }

            $resultado = parent::deletar($id);

            if ($resultado !== false && $resultado !== null) {
                return ["sucesso" => "Sonho deletado com sucesso!"];
            }

            return ["erro" => "Erro ao deletar o sonho"];
        } catch (Exception $e) {
            error_log("Erro ao deletar sonho: " . $e->getMessage());
            return ["erro" => "Erro interno ao deletar sonho"];
        }
    }

    public function buscarPorTag($tag)
    {
        try {
            // Verificar autenticação
            $dadosUsuario = JWTHelper::verificarAutenticacao();
            if (!$dadosUsuario) {
                return ["erro" => "Acesso não autorizado"];
            }

            if (empty($tag)) {
                return ["erro" => "Tag não pode estar vazia"];
            }

            return parent::buscarPorTag($tag);
        } catch (Exception $e) {
            error_log("Erro ao buscar por tag: " . $e->getMessage());
            return ["erro" => "Erro interno ao buscar por tag"];
        }
    }

    private function processarTags($sonho_id, $tags)
    {
        try {
            foreach ($tags as $tagNome) {
                $tagNome = trim($tagNome);
                if (!empty($tagNome)) {
                    // Verificar se a tag existe
                    $tagExistente = $this->tagDAO->buscarPorNome($tagNome);

                    if (empty($tagExistente)) {
                        $tag_id = $this->tagDAO->inserir($tagNome);
                    } else {
                        $tag_id = $tagExistente[0]['id'];
                    }

                    // Associar a tag ao sonho
                    parent::associarTag($sonho_id, $tag_id);
                }
            }
        } catch (Exception $e) {
            error_log("Erro ao processar tags: " . $e->getMessage());
            throw new Exception("Erro ao processar tags do sonho");
        }
    }
}
