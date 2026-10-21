<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

// Cambio OBLIGATORIO de la clave inicial: el Administrador crea el usuario, le pasa la
// contraseña provisoria por fuera del sistema y, en el primer ingreso, esta es la única
// página que puede usar hasta cambiarla (SessionAuth::requireLogin lo fuerza en las demás).
$auth = new SessionAuth();
$usuario = $auth->requireLogin(true);
$idioma = Translator::currentLanguage();
$repo = new UsuarioRepository();
$ci = (int)$usuario['ci_usuario'];

// Si la clave ya fue cambiada no hay nada que hacer acá.
if (!$repo->debeCambiarClave($ci)) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $actual = (string)($_POST['actual'] ?? '');
    $nueva = (string)($_POST['nueva'] ?? '');
    $repetir = (string)($_POST['repetir'] ?? '');
    $hashActual = $repo->obtenerHashClave($ci);

    if (!csrf_valido()) {
        flash_y_volver(t('msgSolicitudInvalida'), false);
    } elseif ($hashActual === null || !password_verify($actual, $hashActual)) {
        // Se cuentan los errores: con 5 se cierra la sesión (igual que en el login).
        $_SESSION['intentos_clave'] = (int)($_SESSION['intentos_clave'] ?? 0) + 1;
        usleep(400000);
        if ($_SESSION['intentos_clave'] >= 5) {
            $auth->logout();
            header('Location: login.php');
            exit;
        }
        flash_y_volver(t('msgClaveActualIncorrecta'), false);
    } elseif ($nueva !== $repetir) {
        flash_y_volver(t('msgClaveNoCoincide'), false);
    } elseif ($nueva === $actual) {
        flash_y_volver(t('msgClaveIgualActual'), false);
    } elseif (($errorClave = SessionAuth::validarClaveNueva($nueva)) !== null) {
        flash_y_volver(t($errorClave), false);
    } elseif (!$repo->cambiarClave($ci, password_hash($nueva, PASSWORD_DEFAULT))) {
        flash_y_volver(t('msgClaveNoGuardada'), false);
    } else {
        session_regenerate_id(true);
        $_SESSION['intentos_clave'] = 0;
        header('Location: index.php');
        exit;
    }
}

$flash = flash_tomar();
?>
<!DOCTYPE html>
<html lang="<?= $idioma ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('tituloCambiarClave') ?></title>
    <link rel="stylesheet" href="CSS/styles.css">
</head>
<body>

<header>
    <h1>SGRSI</h1>
    <p><?= t('appSubtitulo') ?></p>
    <div style="margin-top:8px;font-size:0.85rem;opacity:0.9;">
        <?= e($usuario['nom']) ?> <?= e($usuario['ape']) ?>
    </div>
    <div style="margin-top:6px;font-size:0.8rem;">
        <a href="?idioma=es" style="color:<?= $idioma === 'es' ? '#fff' : '#94a3b8' ?>;text-decoration:none;font-weight:<?= $idioma === 'es' ? '700' : '400' ?>;">ES</a>
        <span style="opacity:0.5;">|</span>
        <a href="?idioma=en" style="color:<?= $idioma === 'en' ? '#fff' : '#94a3b8' ?>;text-decoration:none;font-weight:<?= $idioma === 'en' ? '700' : '400' ?>;">EN</a>
    </div>
</header>

<nav>
    <a href="logout.php" style="margin-left:auto;color:#fca5a5;"><?= t('navCerrarSesion') ?></a>
</nav>

<main>
    <section id="cambiarClave">
        <h2><?= t('cambiarClaveTitulo') ?></h2>
        <p class="mensaje info"><?= t('cambiarClaveAviso') ?></p>

        <?= flash_html($flash) ?>

        <form method="POST" autocomplete="off">
            <?= csrf_campo() ?>
            <label for="actual"><?= t('labelClaveActual') ?></label>
            <input type="password" id="actual" name="actual" autocomplete="current-password" required>

            <label for="nueva"><?= t('labelClaveNueva') ?></label>
            <input type="password" id="nueva" name="nueva" minlength="8" maxlength="72" autocomplete="new-password" required>
            <small style="display:block;margin:-6px 0 12px;color:var(--text-muted);"><?= t('ayudaClaveNueva') ?></small>

            <label for="repetir"><?= t('labelClaveRepetir') ?></label>
            <input type="password" id="repetir" name="repetir" minlength="8" maxlength="72" autocomplete="new-password" required>

            <button type="submit"><?= t('botonCambiarClave') ?></button>
        </form>
    </section>
</main>

<footer>
    <p><?= t('footer') ?></p>
</footer>

</body>
</html>
