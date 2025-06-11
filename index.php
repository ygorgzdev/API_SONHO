<?php
session_start();
include "generic/Autoload.php";


use generic\Controller;

if (isset($_GET["param"])) {
    $controller = new Controller();
    $controller->verificarChamadas($_GET["param"]); // public
}

//carrega autoload, incluindo todas as classes 
//importa classe Controller (generic)
//verifica  param
//new controller + processa req


//autoload
