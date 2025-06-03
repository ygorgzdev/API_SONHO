<?php

namespace dao;

interface ISonhoDAO
{
    public function listar();
    public function inserir($conteudo);
    public function listarPorId($id);
    public function alterar($id, $conteudo);
    public function deletar($id);
    public function buscarPorTag($tag);
    public function associarTag($sonho_id, $tag_id);
    public function removerTodasTags($sonho_id);
}
