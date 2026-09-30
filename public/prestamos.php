<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$auth = new SessionAuth();
$usuario = $auth->requireLogin();
$permiso = $auth->requirePageAccess('prestamos.php', $usuario);
$idioma = Translator::currentLanguage();
$mensaje = '';

$servicio = new PrestamoServicio();
$prestamosRepo = new PrestamoRepository();

$esPersonal = $permiso['ticketsCerrar']; // Administrador y Técnico

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valido()) {
        $mensaje = 'Solicitud no válida, recargá la página';
    } elseif (isset($_POST['devolver'])) {
        if (!$esPersonal) {
            $mensaje = 'No tenés permiso para registrar devoluciones';
        } elseif ($servicio->devolver((int)$_POST['devolver'])) {
            $mensaje = t('msgEquipoDevuelto');
        } else {
            $mensaje = 'Ese préstamo ya estaba devuelto';
        }
    } else {
        // Un solicitante solo puede pedir prestado a su propio nombre.
        $ci = $esPersonal ? (int)($_POST['ciSolicitante'] ?? 0) : (int)$usuario['ci_usuario'];
        $fecha = trim((string)($_POST['fechaFinPrevista'] ?? ''));
        $fechaObj = DateTime::createFromFormat('Y-m-d', $fecha);
        $fechaOk = $fechaObj !== false && $fechaObj->format('Y-m-d') === $fecha && $fecha >= date('Y-m-d');

        if ($ci <= 0 || (int)($_POST['idEquipo'] ?? 0) <= 0) {
            $mensaje = 'Elegí un solicitante y un equipo';
        } elseif (!$fechaOk) {
            $mensaje = 'La fecha de devolución no es válida (no puede ser anterior a hoy)';
        } elseif ($servicio->registrar(new Prestamo(
            $ci,
            (int)$_POST['idEquipo'],
            $fecha,
            mb_substr(trim((string)($_POST['observacion'] ?? '')), 0, 100)
        ))) {
            $mensaje = t('msgPrestamoRegistrado');
        } else {
            $mensaje = 'No se pudo registrar (el equipo ya no está disponible)';
        }
    }
}

$prestamos = $prestamosRepo->listarConDetalle();
$usuarios = (new UsuarioRepository())->listarSolicitantes();
$equiposDisponibles = (new EquipoRepository())->listarDisponibles();
?>
<!DOCTYPE html>
<html lang="<?= $idioma ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('tituloPrestamos') ?></title>
    <link rel="stylesheet" href="CSS/styles.css">
</head>
<body>

<header>
    <h1>SGRSI</h1>
    <p><?= t('appSubtitulo') ?></p>
    <div style="margin-top:8px;font-size:0.85rem;opacity:0.9;">
        <?= e($usuario['nom']) ?> <?= e($usuario['ape']) ?>
        <span class="badge <?= $permiso['clase'] ?>" style="vertical-align:middle;margin-left:4px;"><?= tv($permiso['etiqueta']) ?></span>
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
    <a href="prestamos.php" class="activo"><?= t('navPrestamos') ?></a>
    <?php if (in_array('tickets.php', $permiso['paginas'], true)): ?><a href="tickets.php"><?= t('navTickets') ?></a><?php endif; ?>
    <?php if (in_array('reportes.php', $permiso['paginas'], true)): ?><a href="reportes.php"><?= t('navReportes') ?></a><?php endif; ?>
    <?php if (in_array('historial.php', $permiso['paginas'], true)): ?><a href="historial.php"><?= t('navHistorial') ?></a><?php endif; ?>

    <a href="logout.php" style="margin-left:auto;color:#fca5a5;"><?= t('navCerrarSesion') ?></a>
</nav>

<main>
    <section id="prestamos">
        <h2><?= t('registroPrestamosTitulo') ?></h2>

        <form method="POST">
            <?= csrf_campo() ?>
            <label for="ciSolicitante"><?= t('labelSolicitante') ?></label>
            <select id="ciSolicitante" name="ciSolicitante" required>
                <option value=""><?= t('opcionSeleccionarSolicitante') ?></option>
                <?php foreach ($usuarios as $u): ?>
                    <option value="<?= e($u['ci_usuario']) ?>"><?= e($u['nom']) ?> <?= e($u['ape']) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="idEquipo"><?= t('labelEquipo') ?></label>
            <select id="idEquipo" name="idEquipo" required>
                <option value=""><?= t('opcionSeleccionarEquipo') ?></option>
                <?php foreach ($equiposDisponibles as $e): ?>
                    <option value="<?= e($e['id_equipo']) ?>"><?= e($e['modelo']) ?> (<?= tv($e['tipo']) ?>)</option>
                <?php endforeach; ?>
            </select>

            <label for="fechaFinPrevista"><?= t('labelFechaDevolucion') ?></label>
            <input type="date" id="fechaFinPrevista" name="fechaFinPrevista" required>

            <label for="observacion"><?= t('labelObservacion') ?></label>
            <input type="text" id="observacion" name="observacion" placeholder="<?= t('placeholderObservacion') ?>">

            <button type="submit"><?= t('botonRegistrarPrestamo') ?></button>
        </form>

        <?php if ($mensaje !== ''): ?><p class="mensaje"><?= e($mensaje) ?></p><?php endif; ?>

        <div class="tabla-wrapper">
            <table>
                <thead>
                    <tr><th><?= t('thNum') ?></th><th><?= t('thSolicitante') ?></th><th><?= t('thEquipo') ?></th><th><?= t('thTipo') ?></th><th><?= t('thDevolucion') ?></th><th><?= t('thEstado') ?></th><th><?= t('thAccionCol') ?></th></tr>
                </thead>
                <tbody>
                    <?php $i = 0; foreach ($prestamos as $p): $i++;
                        $prestado = $p['estado'] === 'Entregado';
                    ?>
                        <tr>
                            <td><?= $i ?></td>
                            <td><?= e($p['nom']) ?> <?= e($p['ape']) ?></td>
                            <td><?= e($p['modelo']) ?></td>
                            <td><?= tv($p['tipo']) ?></td>
                            <td><?= e($p['fechaFinPrevista']) ?></td>
                            <td><span class="badge <?= $prestado ? 'badge-warning' : 'badge-success' ?>"><?= tv($p['estado']) ?></span></td>
                            <td>
                                <?php if ($prestado): ?>
                                    <?php if ($esPersonal): ?>
                                    <form method="post" style="display:inline">
                                        <?= csrf_campo() ?>
                                        <input type="hidden" name="devolver" value="<?= e($p['id_prestamo']) ?>">
                                        <button type="submit" class="btn-sm btn-success"><?= t('botonDevolver') ?></button>
                                    </form>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
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
