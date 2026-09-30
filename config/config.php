<?php

declare(strict_types=1);

// Configuración global.
// Las credenciales NO van acá: están en config/secreto.php (no se sube a GitHub).
// Copiá config/secreto.ejemplo.php como config/secreto.php y poné tus datos.

// No mostrar errores en pantalla (pueden revelar rutas, usuarios y claves).
ini_set('display_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

define('DB_HOST', 'localhost');
define('DB_NAME', 'BD_SGRSI');

$secreto = __DIR__ . '/secreto.php';
if (!is_file($secreto)) {
    http_response_code(500);
    exit('Falta el archivo config/secreto.php');
}
require_once $secreto;   // define DB_USER y DB_PASS

define('RAIZ', dirname(__DIR__));

// Autoload: las clases se buscan solo en negocio/ y datos/.
spl_autoload_register(function (string $clase): void {
    foreach (['negocio', 'datos'] as $capa) {
        $ruta = RAIZ . "/$capa/$clase.php";
        if (is_file($ruta)) {
            require_once $ruta;
            return;
        }
    }
});
