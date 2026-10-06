<?php

declare(strict_types=1);

define('ENTRYPOINT', true);

// Punto de entrada común de todas las páginas de public/.
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/Language.php';

$idioma = Translator::currentLanguage();
$GLOBALS['idioma'] = $idioma;

// Escapa texto antes de mostrarlo en pantalla (evita XSS).
function e($valor): string
{
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

// --- Protección CSRF: cada formulario que modifica datos lleva este token ---
function csrf_token(): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function csrf_campo(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_valido(): bool
{
    return isset($_POST['csrf'], $_SESSION['csrf'])
        && hash_equals($_SESSION['csrf'], (string)$_POST['csrf']);
}

