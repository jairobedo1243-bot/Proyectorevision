<?php

declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';

$auth = new SessionAuth();
$usuario = $auth->requireLogin();
$permiso = $auth->requirePageAccess('prestamos.php', $usuario);
$idioma = Translator::currentLanguage();
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ciSolicitante = (int)($_POST['ciSolicitante'] ?? 0);
    $idEquipo = (int)($_POST['idEquipo'] ?? 0);
    $fechaFinPrevista = trim((string)($_POST['fechaFinPrevista'] ?? ''));
    $observacion = trim((string)($_POST['observacion'] ?? ''));

    Database::execute(
        "INSERT INTO prestamo (fechaFinPrevista, observacion, estado, ci_solicitante, id_equipo) VALUES (?, ?, 'Entregado', ?, ?)",
        [$fechaFinPrevista, $observacion, $ciSolicitante, $idEquipo]
    );
    Database::execute("UPDATE equipo SET estado = 'Prestado' WHERE id_equipo = ?", [$idEquipo]);

    $mensaje = t('msgPrestamoRegistrado');
}

if (isset($_GET['devolver'])) {
    $idPrestamo = (int)$_GET['devolver'];
    Database::execute("UPDATE prestamo SET estado = 'Devuelto', fechaDevolucion = NOW() WHERE id_prestamo = ?", [$idPrestamo]);
    Database::execute(
        "UPDATE equipo e JOIN prestamo p ON p.id_equipo = e.id_equipo SET e.estado = 'Disponible' WHERE p.id_prestamo = ?",
        [$idPrestamo]
    );
    $mensaje = t('msgEquipoDevuelto');
}

$prestamos = Database::select(
    "SELECT p.*, u.nom, u.ape, e.modelo, e.marca, e.tipo FROM prestamo p JOIN usuario u ON p.ci_solicitante = u.ci_usuario JOIN equipo e ON p.id_equipo = e.id_equipo ORDER BY p.id_prestamo DESC"
);
$usuarios = Database::select('SELECT ci_usuario, nom, ape FROM usuario ORDER BY nom');
$equiposDisponibles = Database::select("SELECT * FROM equipo WHERE estado = 'Disponible'");
?>
<!DOCTYPE html>
<html lang="<?= $idioma ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('tituloPrestamos') ?></title>
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
            <label for="ciSolicitante"><?= t('labelSolicitante') ?></label>
            <select id="ciSolicitante" name="ciSolicitante" required>
                <option value=""><?= t('opcionSeleccionarSolicitante') ?></option>
                <?php foreach ($usuarios as $u): ?>
                    <option value="<?= $u['ci_usuario'] ?>"><?= $u['nom'] ?> <?= $u['ape'] ?></option>
                <?php endforeach; ?>
            </select>

            <label for="idEquipo"><?= t('labelEquipo') ?></label>
            <select id="idEquipo" name="idEquipo" required>
                <option value=""><?= t('opcionSeleccionarEquipo') ?></option>
                <?php foreach ($equiposDisponibles as $e): ?>
                    <option value="<?= $e['id_equipo'] ?>"><?= $e['modelo'] ?> (<?= tv($e['tipo']) ?>)</option>
                <?php endforeach; ?>
            </select>

            <label for="fechaFinPrevista"><?= t('labelFechaDevolucion') ?></label>
            <input type="date" id="fechaFinPrevista" name="fechaFinPrevista" required>

            <label for="observacion"><?= t('labelObservacion') ?></label>
            <input type="text" id="observacion" name="observacion" placeholder="<?= t('placeholderObservacion') ?>">

            <button type="submit"><?= t('botonRegistrarPrestamo') ?></button>
        </form>

        <?php if ($mensaje !== ''): ?><p class="mensaje"><?= $mensaje ?></p><?php endif; ?>

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
                            <td><?= $p['nom'] ?> <?= $p['ape'] ?></td>
                            <td><?= $p['modelo'] ?></td>
                            <td><?= tv($p['tipo']) ?></td>
                            <td><?= $p['fechaFinPrevista'] ?></td>
                            <td><span class="badge <?= $prestado ? 'badge-warning' : 'badge-success' ?>"><?= tv($p['estado']) ?></span></td>
                            <td>
                                <?php if ($prestado): ?>
                                    <a class="btn-sm btn-success" href="prestamos.php?devolver=<?= $p['id_prestamo'] ?>"><?= t('botonDevolver') ?></a>
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
