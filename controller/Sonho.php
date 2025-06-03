<?php

namespace controller;

use service\SonhoService;

//nao valida
//nao acessa bd
//faz ajustes ,
//lógica vai pro service
//complexidade esta em generic

class Sonho
{
    public function listar()
    {
        $service = new SonhoService();
        $resultado = $service->listarSonhos();
        return $resultado;
    }

    public function inserir($conteudo, $tags = null)
    {
        $service = new SonhoService();
        $tagArray = $tags ? explode(',', $tags) : [];
        $resultado = $service->inserir($conteudo, $tagArray);
        return $resultado;
    }

    public function buscarPorId($id)
    {
        $service = new SonhoService();
        $resultado = $service->listarPorId($id);
        return $resultado;
    }

    public function atualizar($id, $conteudo, $tags = null)
    {
        $service = new SonhoService();
        $tagArray = $tags ? explode(',', $tags) : [];
        $resultado = $service->alterar($id, $conteudo, $tagArray);
        return $resultado;
    }

    public function deletar($id)
    {
        $service = new SonhoService();
        $resultado = $service->deletar($id);
        return $resultado;
    }

    public function buscarPorTag($tag)
    {
        $service = new SonhoService();
        $resultado = $service->buscarPorTag($tag);
        return $resultado;
    }
}


//services