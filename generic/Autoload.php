<?php
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

spl_autoload_register(function ($class) {

    //namespasce para diretorio correto (raiz)\\
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    $default_file_path = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . $path; //caminho absoluto - raiz

    if (file_exists($default_file_path)) {
        include_once $default_file_path;
        return; //inclui arq se existe + return n executa mais 
    }

    // exceção (se n econtra) para encontrar as interfaces na pasta 'dao/interfaces'
    //namespace = dao - procura interface em dao/interface/name.php
    $parts = explode('\\', $class);
    if (count($parts) === 2 && $parts[0] === 'dao') {
        $interface_path = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'dao' . DIRECTORY_SEPARATOR . 'interfaces' . DIRECTORY_SEPARATOR . $parts[1] . '.php';
        if (file_exists($interface_path)) {
            include_once $interface_path;
            return;
        }
    }
});

//evita include manual

//controller gen