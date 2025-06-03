<?php

namespace dao;

interface ITagDAO
{
    public function listar();
    public function inserir($nome);
    public function listarPorId($id);
    public function buscarPorNome($nome);
    public function alterar($id, $nome);
    public function deletar($id);
    public function tagsMaisUsadas();
}
