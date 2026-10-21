<?php

declare(strict_types=1);

define('ENTRYPOINT', true);

// Punto de entrada común de todas las páginas de public/.
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/Language.php';

$idioma = Translator::currentLanguage();
$GLOBALS['idioma'] = $idioma;

// Si algo falla (por ejemplo, MySQL apagado o mal configurado) se muestra una
// página clara en vez de una pantalla en blanco. El detalle queda en el log de Apache.
set_exception_handler(function (Throwable $ex): void {
    error_log('SGRSI: ' . get_class($ex) . ': ' . $ex->getMessage() . ' en ' . $ex->getFile() . ':' . $ex->getLine());
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: text/html; charset=UTF-8');
    }
    $sinBase = $ex instanceof RuntimeException || $ex instanceof mysqli_sql_exception;
    $detalle = $sinBase
        ? 'No se pudo trabajar con la base de datos. Revisá que MySQL esté iniciado en XAMPP, que la base BD_SGRSI esté importada (sql/BD_SGRSI.sql) y que el usuario, la clave y el puerto de config/secreto.php sean correctos.'
        : 'Ocurrió un error inesperado.';
    echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>SGRSI – Error</title>'
        . '<link rel="stylesheet" href="CSS/styles.css"></head><body>'
        . '<header><h1>SGRSI</h1></header><main><section><h2>Error</h2>'
        . '<p class="mensaje error">' . htmlspecialchars($detalle, ENT_QUOTES, 'UTF-8') . '</p>'
        . '<p style="margin-top:12px;font-size:0.85rem;color:var(--text-muted)">El detalle técnico quedó en C:\\xampp\\apache\\logs\\error.log</p>'
        . '<p style="margin-top:12px"><a href="login.php">Volver al inicio</a></p>'
        . '</section></main></body></html>';
});

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
        && is_string($_POST['csrf'])
        && hash_equals($_SESSION['csrf'], $_POST['csrf']);
}

// --- Mensajes después de un formulario (patrón POST → redirección → GET) ---
// Así, si el usuario aprieta F5, el navegador no vuelve a mandar el formulario
// (antes se duplicaban tickets, préstamos, etc.).
function flash_y_volver(string $texto, bool $exito): void
{
    $_SESSION['flash'] = ['texto' => $texto, 'exito' => $exito];
    header('Location: ' . basename((string)$_SERVER['SCRIPT_NAME']));
    exit;
}

function flash_tomar(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return is_array($flash) ? $flash : null;
}

function flash_html(?array $flash): string
{
    if ($flash === null) {
        return '';
    }
    return '<p class="mensaje ' . ($flash['exito'] ? 'exito' : 'error') . '">' . e($flash['texto']) . '</p>';
}
