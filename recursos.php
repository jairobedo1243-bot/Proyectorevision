<?php

declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';

$auth = new SessionAuth();
$usuario = $auth->requireLogin();
$permiso = $auth->requirePageAccess('recursos.php', $usuario);
$idioma = Translator::currentLanguage();
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $permiso['inventarioEditar']) {
    $numeroSerie = trim((string)($_POST['numeroSerie'] ?? ''));
    $modelo = trim((string)($_POST['modelo'] ?? ''));
    $marca = trim((string)($_POST['marca'] ?? ''));
    $tipo = (string)($_POST['tipo'] ?? '');

    Database::execute(
        "INSERT INTO equipo (estado, numeroSerie, modelo, marca, tipo) VALUES ('Disponible', ?, ?, ?, ?)",
        [$numeroSerie, $modelo, $marca, $tipo]
    );

    $mensaje = t('msgEquipoAgregado') . $modelo;
}

$resultado = Database::select("SELECT * FROM equipo ORDER BY id_equipo DESC");
?>
<!DOCTYPE html>
<html lang="<?= $idioma ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('tituloInventario') ?></title>
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
    <a href="recursos.php" class="activo"><?= t('navInventario') ?></a>
    <?php if (in_array('usuarios.php', $permiso['paginas'], true)): ?><a href="usuarios.php"><?= t('navUsuarios') ?></a><?php endif; ?>
    <?php if (in_array('prestamos.php', $permiso['paginas'], true)): ?><a href="prestamos.php"><?= t('navPrestamos') ?></a><?php endif; ?>
    <?php if (in_array('tickets.php', $permiso['paginas'], true)): ?><a href="tickets.php"><?= t('navTickets') ?></a><?php endif; ?>
    <?php if (in_array('reportes.php', $permiso['paginas'], true)): ?><a href="reportes.php"><?= t('navReportes') ?></a><?php endif; ?>
    <?php if (in_array('historial.php', $permiso['paginas'], true)): ?><a href="historial.php"><?= t('navHistorial') ?></a><?php endif; ?>

    <a href="logout.php" style="margin-left:auto;color:#fca5a5;"><?= t('navCerrarSesion') ?></a>
</nav>

<main>
    <section id="registroEquipo">
        <h2><?= t('registroEquiposTitulo') ?></h2>

        <?php if ($permiso['inventarioEditar']): ?>
            <form method="POST">
                <label for="modeloEquipo"><?= t('labelModelo') ?></label>
                <input type="text" id="modeloEquipo" name="modelo" placeholder="<?= t('placeholderModelo') ?>" required>

                <label for="marcaEquipo"><?= t('labelMarca') ?></label>
                <input type="text" id="marcaEquipo" name="marca" placeholder="<?= t('placeholderMarca') ?>" required>

                <label for="numeroSerieEquipo"><?= t('labelNumeroSerie') ?></label>
                <input type="text" id="numeroSerieEquipo" name="numeroSerie" placeholder="<?= t('placeholderNumeroSerie') ?>" required>

                <label for="tipoEquipo"><?= t('labelTipoEquipo') ?></label>
                <select id="tipoEquipo" name="tipo" required>
                    <option value=""><?= t('opcionSeleccionar') ?></option>
                    <option value="PC"><?= tv('PC') ?></option>
                    <option value="Laptop"><?= tv('Laptop') ?></option>
                    <option value="Monitor"><?= tv('Monitor') ?></option>
                    <option value="Proyector"><?= tv('Proyector') ?></option>
                    <option value="Television"><?= tv('Television') ?></option>
                    <option value="Otro"><?= tv('Otro') ?></option>
                </select>

                <button type="submit"><?= t('botonAgregarEquipo') ?></button>
            </form>
        <?php else: ?>
            <p class="mensaje info"><?= t('avisoSoloConsultaInventario') ?></p>
        <?php endif; ?>

        <?php if ($mensaje !== ''): ?><p class="mensaje"><?= $mensaje ?></p><?php endif; ?>

        <div class="tabla-wrapper">
            <table>
                <thead>
                    <tr><th><?= t('thNum') ?></th><th><?= t('thModelo') ?></th><th><?= t('thMarca') ?></th><th><?= t('thNSerie') ?></th><th><?= t('thTipo') ?></th><th><?= t('thEstado') ?></th></tr>
                </thead>
                <tbody>
                    <?php $i = 0; foreach ($resultado as $fila): $i++;
                        $badgeClase = $fila['estado'] === 'Disponible' ? 'badge-success' : ($fila['estado'] === 'Prestado' ? 'badge-warning' : 'badge-danger');
                    ?>
                        <tr>
                            <td><?= $i ?></td>
                            <td><?= $fila['modelo'] ?></td>
                            <td><?= $fila['marca'] ?></td>
                            <td><?= $fila['numeroSerie'] ?></td>
                            <td><?= tv($fila['tipo']) ?></td>
                            <td><span class="badge <?= $badgeClase ?>"><?= tv($fila['estado']) ?></span></td>
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
