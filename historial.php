<?php

declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';

$auth = new SessionAuth();
$usuario = $auth->requireLogin();
$permiso = $auth->requirePageAccess('historial.php', $usuario);
$idioma = Translator::currentLanguage();

$resultado = Database::select(
    "SELECT h.*, e.modelo, e.marca, t.descripcion AS descripcionTicket, s.descripcion AS descripcionServicio FROM historial h JOIN equipo e ON h.id_equipo = e.id_equipo LEFT JOIN ticket t ON h.id_ticket = t.id_ticket LEFT JOIN servicio s ON h.id_servicio = s.id_servicio ORDER BY h.id_historial DESC"
);
?>
<!DOCTYPE html>
<html lang="<?= $idioma ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('tituloHistorial') ?></title>
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
    <?php if (in_array('reportes.php', $permiso['paginas'], true)): ?><a href="reportes.php"><?= t('navReportes') ?></a><?php endif; ?>
    <a href="historial.php" class="activo"><?= t('navHistorial') ?></a>

    <a href="logout.php" style="margin-left:auto;color:#fca5a5;"><?= t('navCerrarSesion') ?></a>
</nav>

<main>
    <section id="Historial">
        <h2><?= t('historialTitulo') ?></h2>

        <div class="tabla-wrapper">
            <table>
                <thead>
                    <tr><th><?= t('thNum') ?></th><th><?= t('thDescripcion') ?></th><th><?= t('thAccionCol') ?></th><th><?= t('thEquipo') ?></th><th><?= t('thTicket') ?></th><th><?= t('thServicio') ?></th></tr>
                </thead>
                <tbody>
                    <?php $i = 0; foreach ($resultado as $fila): $i++; ?>
                        <tr>
                            <td><?= $i ?></td>
                            <td><?= $fila['descripcion'] ?></td>
                            <td><span class="badge badge-info"><?= $fila['accion'] ?></span></td>
                            <td><?= $fila['modelo'] ?> <?= $fila['marca'] ?></td>
                            <td><?= $fila['descripcionTicket'] ?></td>
                            <td><?= $fila['descripcionServicio'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<footer>
    <p><?= t('footer') ?></p>
</footer>

</body>
</html>
