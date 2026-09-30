<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$auth = new SessionAuth();
$usuario = $auth->requireLogin();
$permiso = $auth->requirePageAccess('tickets.php', $usuario);
$idioma = Translator::currentLanguage();
$mensaje = '';

$ticketsRepo = new TicketRepository();

$esPersonal = $permiso['ticketsCerrar']; // Administrador y Técnico

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valido()) {
        $mensaje = 'Solicitud no válida, recargá la página';
    } elseif (isset($_POST['cerrar'])) {
        if ($esPersonal) {
            $ticketsRepo->cerrar((int)$_POST['cerrar']);
            $mensaje = t('msgTicketCerrado');
        } else {
            $mensaje = 'No tenés permiso para cerrar tickets';
        }
    } else {
        // Un solicitante solo puede abrir tickets a su propio nombre.
        $ci = $esPersonal ? (int)($_POST['ciSolicitante'] ?? 0) : (int)$usuario['ci_usuario'];
        $titulo = mb_substr(trim((string)($_POST['titulo'] ?? '')), 0, 60);
        $descripcion = mb_substr(trim((string)($_POST['descripcion'] ?? '')), 0, 100);
        $prioridad = (string)($_POST['prioridad'] ?? '');

        if ($ci <= 0 || $titulo === '' || $descripcion === '' || !in_array($prioridad, ['Alta', 'Media', 'Baja'], true)) {
            $mensaje = 'Completá todos los campos correctamente';
        } elseif ($ticketsRepo->guardar(new Ticket($titulo, $descripcion, $ci, $prioridad))) {
            $mensaje = t('msgTicketCreado');
        } else {
            $mensaje = 'No se pudo crear el ticket';
        }
    }
}

$tickets = $ticketsRepo->listarConSolicitante();
$usuarios = (new UsuarioRepository())->listarSolicitantesYTecnicos();
?>
<!DOCTYPE html>
<html lang="<?= $idioma ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('tituloTickets') ?></title>
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
    <?php if (in_array('prestamos.php', $permiso['paginas'], true)): ?><a href="prestamos.php"><?= t('navPrestamos') ?></a><?php endif; ?>
    <a href="tickets.php" class="activo"><?= t('navTickets') ?></a>
    <?php if (in_array('reportes.php', $permiso['paginas'], true)): ?><a href="reportes.php"><?= t('navReportes') ?></a><?php endif; ?>
    <?php if (in_array('historial.php', $permiso['paginas'], true)): ?><a href="historial.php"><?= t('navHistorial') ?></a><?php endif; ?>
    <a href="logout.php" style="margin-left:auto;color:#fca5a5;"><?= t('navCerrarSesion') ?></a>
</nav>

<main>
    <section id="tickets">
        <h2><?= t('mesaAyudaTitulo') ?></h2>

        <form method="POST">
            <?= csrf_campo() ?>
            <label for="ciSolicitante"><?= t('labelSolicitante') ?></label>
            <select id="ciSolicitante" name="ciSolicitante" required>
                <option value=""><?= t('opcionSeleccionarSolicitante') ?></option>
                <?php foreach ($usuarios as $u): ?>
                    <option value="<?= e($u['ci_usuario']) ?>"><?= e($u['nom']) ?> <?= e($u['ape']) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="titulo"><?= t('labelTitulo') ?></label>
            <input type="text" id="titulo" name="titulo" placeholder="<?= t('placeholderTitulo') ?>" required>

            <label for="prioridad"><?= t('labelPrioridad') ?></label>
            <select id="prioridad" name="prioridad" required>
                <option value=""><?= t('opcionSeleccionar') ?></option>
                <option value="Alta"><?= tv('Alta') ?></option>
                <option value="Media"><?= tv('Media') ?></option>
                <option value="Baja"><?= tv('Baja') ?></option>
            </select>

            <label for="descripcion"><?= t('labelDescripcionProblema') ?></label>
            <textarea id="descripcion" name="descripcion" placeholder="<?= t('placeholderDescripcion') ?>" required></textarea>

            <button type="submit"><?= t('botonCrearTicket') ?></button>
        </form>

        <?php if ($mensaje !== ''): ?><p class="mensaje"><?= e($mensaje) ?></p><?php endif; ?>

        <div class="tabla-wrapper">
            <table>
                <thead>
                    <tr><th><?= t('thNum') ?></th><th><?= t('thFecha') ?></th><th><?= t('thSolicitante') ?></th><th><?= t('thTitulo') ?></th><th><?= t('thPrioridad') ?></th><th><?= t('thProblema') ?></th><th><?= t('thEstado') ?></th><th><?= t('thAccionCol') ?></th></tr>
                </thead>
                <tbody>
                    <?php $i = 0; foreach ($tickets as $t): $i++;
                        $abierto = $t['estado'] !== 'Cerrado';
                        $prioClase = $t['prioridad'] === 'Alta' ? 'badge-danger' : ($t['prioridad'] === 'Media' ? 'badge-warning' : 'badge-info');
                    ?>
                        <tr>
                            <td><?= $i ?></td>
                            <td><?= e($t['fechaCreacion']) ?></td>
                            <td><?= e($t['nom']) ?> <?= e($t['ape']) ?></td>
                            <td><?= e($t['titulo']) ?></td>
                            <td><span class="badge <?= $prioClase ?>"><?= tv($t['prioridad']) ?></span></td>
                            <td><?= e($t['descripcion']) ?></td>
                            <td><span class="badge <?= $abierto ? 'badge-warning' : 'badge-success' ?>"><?= tv($t['estado']) ?></span></td>
                            <td>
                                <?php if ($abierto && $permiso['ticketsCerrar']): ?>
                                    <form method="post" style="display:inline">
                                        <?= csrf_campo() ?>
                                        <input type="hidden" name="cerrar" value="<?= e($t['id_ticket']) ?>">
                                        <button type="submit" class="btn-sm btn-success"><?= t('botonCerrar') ?></button>
                                    </form>
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
