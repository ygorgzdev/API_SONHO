<?php

namespace controller;

use service\InterpretacaoService;

class Interpretacao
{
    public function listar()
    {
        $service = new InterpretacaoService();
        $resultado = $service->listarInterpretacoes();
        return $resultado;
    }

    public function inserir($sonho_id, $interpretador, $texto)
    {
        $service = new InterpretacaoService();
        $resultado = $service->inserir($sonho_id, $interpretador, $texto);
        return $resultado;
    }

    public function buscarPorId($id)
    {
        $service = new InterpretacaoService();
        $resultado = $service->listarPorId($id);
        return $resultado;
    }

    public function buscarPorSonho($sonho_id)
    {
        $service = new InterpretacaoService();
        $resultado = $service->listarPorSonho($sonho_id);
        return $resultado;
    }

    public function atualizar($id, $interpretador, $texto)
    {
        $service = new InterpretacaoService();
        $resultado = $service->alterar($id, $interpretador, $texto);
        return $resultado;
    }

    public function deletar($id)
    {
        $service = new InterpretacaoService();
        $resultado = $service->deletar($id);
        return $resultado;
    }
}
