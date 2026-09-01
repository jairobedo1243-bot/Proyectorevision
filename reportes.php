<?php

declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';

$auth = new SessionAuth();
$usuario = $auth->requireLogin();
$permiso = $auth->requirePageAccess('reportes.php', $usuario);
$idioma = Translator::currentLanguage();

$equiposTotal = (int)Database::scalar('SELECT COUNT(*) AS n FROM equipo');
$equiposDisponibles = (int)Database::scalar("SELECT COUNT(*) AS n FROM equipo WHERE estado = 'Disponible'");
$equiposPrestados = (int)Database::scalar("SELECT COUNT(*) AS n FROM equipo WHERE estado = 'Prestado'");
$ticketsAbiertos = (int)Database::scalar("SELECT COUNT(*) AS n FROM ticket WHERE estado != 'Cerrado'");
$ticketsCerrados = (int)Database::scalar("SELECT COUNT(*) AS n FROM ticket WHERE estado = 'Cerrado'");
$prestamosTotal = (int)Database::scalar('SELECT COUNT(*) AS n FROM prestamo');
$prestamosActivos = (int)Database::scalar("SELECT COUNT(*) AS n FROM prestamo WHERE estado = 'Entregado'");
$prestamosDevueltos = (int)Database::scalar("SELECT COUNT(*) AS n FROM prestamo WHERE estado = 'Devuelto'");
$fecha = date('d/m/Y');
?>
<!DOCTYPE html>
<html lang="<?= $idioma ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('tituloReportes') ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header>
    <h1>SGRSI</h1>
    <p><?= t('appSubtitulo') ?></p>
    <div style="margin-top:8px;font-size:0.85rem;opacity:0.9;">
        <?= $usuario['nom'] ?> <?= $usuario['ape'] ?>
        <span class="badge <?= $permiso['clase'] ?>" style="vertical-align:middle;margin-left:4px;"><?= $permiso['etiqueta'] ?></span>
    </div>
    <div style="margin-top:6px;font-size:0.8rem;">
        <a href="?idioma=es" style="color:<?= $idioma === 'es' ? '#fff' : '#94a3b8' ?>;text-decoration:none;font-weight:<?= $idioma === 'es' ? '700' : '400' ?>;">ES</a>
        <span style="opacity:0.5;">|</span>
        <a href="?idioma=en" style="color:<?= $idioma === 'en' ? '#fff' : '#94a3b8' ?>;text-decoration:none;font-weight:<?= $idioma === 'en' ? '700' : '400' ?>;">EN</a>
    </div>
</header>

<nav>
    <a href="index.php"><?= t('navInicio') ?></a>
    <?php if (in_array('recursos.php', $permiso['paginas'], true)): ?><a href="recursos.php"><?= t('navInventario') ?></a><?php endif; ?>
    <?php if (in_array('usuarios.php', $permiso['paginas'], true)): ?><a href="usuarios.php"><?= t('navUsuarios') ?></a><?php endif; ?>
    <?php if (in_array('prestamos.php', $permiso['paginas'], true)): ?><a href="prestamos.php"><?= t('navPrestamos') ?></a><?php endif; ?>
    <?php if (in_array('tickets.php', $permiso['paginas'], true)): ?><a href="tickets.php"><?= t('navTickets') ?></a><?php endif; ?>
    <a href="reportes.php" class="activo"><?= t('navReportes') ?></a>
    <?php if (in_array('historial.php', $permiso['paginas'], true)): ?><a href="historial.php"><?= t('navHistorial') ?></a><?php endif; ?>

    <a href="logout.php" style="margin-left:auto;color:#fca5a5;"><?= t('navCerrarSesion') ?></a>
</nav>

<main>
    <section id="reportes">
        <h2><?= t('reportesTitulo') ?></h2>
        <p style="color: var(--text-muted); margin-bottom: 16px; font-size:0.92rem;">
            <?= t('reportesDesc') ?>
        </p>

        <div class="stats-grid">
            <div class="stat-card" style="border-top-color:#1e3a5f">
                <div class="numero" style="color:#1e3a5f"><?= $equiposTotal ?></div>
                <div class="etiqueta"><?= t('statEquiposTotales') ?></div>
            </div>
            <div class="stat-card" style="border-top-color:#16a34a">
                <div class="numero" style="color:#16a34a"><?= $equiposDisponibles ?></div>
                <div class="etiqueta"><?= t('statDisponibles') ?></div>
            </div>
            <div class="stat-card" style="border-top-color:#d97706">
                <div class="numero" style="color:#d97706"><?= $equiposPrestados ?></div>
                <div class="etiqueta"><?= t('statPrestados') ?></div>
            </div>
            <div class="stat-card" style="border-top-color:#dc2626">
                <div class="numero" style="color:#dc2626"><?= $ticketsAbiertos ?></div>
                <div class="etiqueta"><?= t('statTicketsAbiertos') ?></div>
            </div>
            <div class="stat-card" style="border-top-color:#16a34a">
                <div class="numero" style="color:#16a34a"><?= $ticketsCerrados ?></div>
                <div class="etiqueta"><?= t('statTicketsCerrados') ?></div>
            </div>
            <div class="stat-card" style="border-top-color:#d97706">
                <div class="numero" style="color:#d97706"><?= $prestamosActivos ?></div>
                <div class="etiqueta"><?= t('statPrestamosActivos') ?></div>
            </div>
        </div>

        <div class="tabla-wrapper">
            <table>
                <thead><tr><th><?= t('thConcepto') ?></th><th><?= t('thValor') ?></th></tr></thead>
                <tbody>
                    <tr><td><?= t('filaTotalEquipos') ?></td><td><strong><?= $equiposTotal ?></strong></td></tr>
                    <tr><td><?= t('filaEquiposDisponibles') ?></td><td><strong><?= $equiposDisponibles ?></strong></td></tr>
                    <tr><td><?= t('filaEquiposPrestados') ?></td><td><strong><?= $equiposPrestados ?></strong></td></tr>
                    <tr><td><?= t('filaTicketsAbiertos') ?></td><td><strong><?= $ticketsAbiertos ?></strong></td></tr>
                    <tr><td><?= t('filaTicketsCerrados') ?></td><td><strong><?= $ticketsCerrados ?></strong></td></tr>
                    <tr><td><?= t('filaPrestamosActivos') ?></td><td><strong><?= $prestamosActivos ?></strong></td></tr>
                    <tr><td><?= t('filaPrestamosDevueltos') ?></td><td><strong><?= $prestamosDevueltos ?></strong></td></tr>
                </tbody>
            </table>
        </div>
        <p style="margin-top:12px;font-size:0.8rem;color:var(--text-muted)"><?= t('reporteGenerado') ?><?= $fecha ?></p>
    </section>
</main>

<footer>
    <p><?= t('footer') ?></p>
</footer>

</body>
</html>
