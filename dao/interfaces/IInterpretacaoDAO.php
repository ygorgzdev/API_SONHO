<?php

namespace dao;

interface IInterpretacaoDAO
{
    public function listar();
    public function inserir($sonho_id, $interpretador, $texto);
    public function listarPorId($id);
    public function listarPorSonho($sonho_id);
    public function alterar($id, $interpretador, $texto);
    public function deletar($id);
}
