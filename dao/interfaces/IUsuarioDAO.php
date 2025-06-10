<?php

namespace dao;

interface IUsuarioDAO
{
    public function buscarPorEmail($email);
    public function inserir($email, $senha, $nome);
    public function listarPorId($id);
}
