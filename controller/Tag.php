<?php

namespace controller;

use service\TagService;

class Tag
{
    public function listar()
    {
        $service = new TagService();
        $resultado = $service->listarTags();
        return $resultado;
    }

    public function inserir($nome)
    {
        $service = new TagService();
        $resultado = $service->inserir($nome);
        return $resultado;
    }

    public function buscarPorId($id)
    {
        $service = new TagService();
        $resultado = $service->listarPorId($id);
        return $resultado;
    }

    public function atualizar($id, $nome)
    {
        $service = new TagService();
        $resultado = $service->alterar($id, $nome);
        return $resultado;
    }

    public function deletar($id)
    {
        $service = new TagService();
        $resultado = $service->deletar($id);
        return $resultado;
    }

    public function maisUsadas()
    {
        $service = new TagService();
        $resultado = $service->tagsMaisUsadas();
        return $resultado;
    }
}
