<?php

namespace service;

use dao\mysql\SonhoDAO;
use dao\mysql\TagDAO;

//validação, coordena, verifica

class SonhoService extends SonhoDAO
{
    private TagDAO $tagDAO;

    public function __construct()
    {
        parent::__construct();
        $this->tagDAO = new TagDAO();
    }

    public function listarSonhos()
    {
        return parent::listar();
    }

    public function inserir($conteudo, $tags = [])
    {
        if (empty($conteudo) || strlen(trim($conteudo)) < 10) {
            return ["erro" => "O conteúdo do sonho deve ter pelo menos 10 caracteres"];
        }

        $sonho_id = parent::inserir($conteudo);

        if ($sonho_id && !empty($tags)) {
            $this->processarTags($sonho_id, $tags);
        }

        if ($sonho_id) {
            return ["sucesso" => "Sonho salvo com sucesso!", "id" => $sonho_id];
        }

        return ["erro" => "Erro ao salvar o sonho"];
    }

    public function listarPorId($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            return ["erro" => "ID inválido"];
        }

        return parent::listarPorId($id);
    }

    public function alterar($id, $conteudo, $tags = [])
    {
        if (!is_numeric($id) || $id <= 0) {
            return ["erro" => "ID inválido"];
        }

        if (empty($conteudo) || strlen(trim($conteudo)) < 10) {
            return ["erro" => "O conteúdo do sonho deve ter pelo menos 10 caracteres"];
        }

        //existencia do sonho antes de tentar atualizar
        $sonhoExistente = parent::listarPorId($id);
        if (empty($sonhoExistente)) {
            return ["erro" => "Sonho não encontrado"];
        }

        $resultado = parent::alterar($id, $conteudo);


        if ($resultado !== false && $resultado !== null) {

            //tira todas as tags antigas e adiciona as novas
            parent::removerTodasTags($id);
            if (!empty($tags)) {
                $this->processarTags($id, $tags);
            }
            return ["sucesso" => "Sonho atualizado com sucesso!"];
        }

        return ["erro" => "Erro ao atualizar o sonho"];
    }

    public function deletar($id)
    {
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
    }

    public function buscarPorTag($tag)
    {
        if (empty($tag)) {
            return ["erro" => "Tag não pode estar vazia"];
        }

        return parent::buscarPorTag($tag);
    }

    private function processarTags($sonho_id, $tags)
    {
        foreach ($tags as $tagNome) {
            $tagNome = trim($tagNome);
            if (!empty($tagNome)) {
                //existencia da tag
                $tagExistente = $this->tagDAO->buscarPorNome($tagNome);

                if (empty($tagExistente)) {
                    $tag_id = $this->tagDAO->inserir($tagNome);
                } else {
                    $tag_id = $tagExistente[0]['id'];
                }

                //associa a tag ao sonho
                parent::associarTag($sonho_id, $tag_id);
            }
        }
    }
}


//daos