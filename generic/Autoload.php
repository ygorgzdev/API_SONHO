<?php
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

spl_autoload_register(function ($class) {

    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    $default_file_path = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . $path;

    if (file_exists($default_file_path)) {
        include_once $default_file_path;
        return;
    }

    $parts = explode('\\', $class);
    if (count($parts) === 2 && $parts[0] === 'dao') {
        $interface_path = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'dao' . DIRECTORY_SEPARATOR . 'interfaces' . DIRECTORY_SEPARATOR . $parts[1] . '.php';
        if (file_exists($interface_path)) {
            include_once $interface_path;
            return;
        }
    }
});
