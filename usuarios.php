<?php

declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';

$auth = new SessionAuth();
$usuario = $auth->requireLogin();
$permiso = $auth->requirePageAccess('usuarios.php', $usuario);
$idioma = Translator::currentLanguage();
$mensaje = '';

function validarCedula(string $cedula): bool
{
    $ci = preg_replace('/[^0-9]/', '', $cedula);
    if (strlen($ci) !== 8) {
        return false;
    }

    $pesos = [2, 9, 8, 7, 6, 3, 4];
    $suma = 0;
    for ($i = 0; $i < 7; $i++) {
        $suma += (int)$ci[$i] * $pesos[$i];
    }

    $verificador = (10 - $suma % 10) % 10;
    return $verificador === (int)$ci[7];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cedula = trim((string)($_POST['cedula'] ?? ''));
    $nombre = trim((string)($_POST['nombre'] ?? ''));
    $correo = trim((string)($_POST['correo'] ?? ''));
    $rol = (string)($_POST['rol'] ?? '');

    if (!validarCedula($cedula)) {
        $mensaje = t('msgCedulaInvalida');
    } else {
        $ci = (int)preg_replace('/[^0-9]/', '', $cedula);
        $partes = explode(' ', $nombre, 2);
        $nom = $partes[0];
        $ape = $partes[1] ?? '';
        $pass = password_hash('Default123!', PASSWORD_DEFAULT);

        Database::execute(
            'INSERT INTO usuario (ci_usuario, nom, ape, email, contrasena, rol) VALUES (?, ?, ?, ?, ?, ?)',
            [$ci, $nom, $ape, $correo, $pass, $rol]
        );

        $mensaje = t('msgUsuarioAgregado') . $nombre;
    }
}

if (isset($_GET['eliminar'])) {
    $id = (int)$_GET['eliminar'];
    Database::execute('DELETE FROM usuario WHERE ci_usuario = ?', [$id]);
    $mensaje = t('msgUsuarioEliminado');
}

$resultado = Database::select('SELECT ci_usuario, nom, ape, email, rol FROM usuario ORDER BY ci_usuario DESC');
?>
<!DOCTYPE html>
<html lang="<?= $idioma ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('tituloUsuarios') ?></title>
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
    <a href="usuarios.php" class="activo"><?= t('navUsuarios') ?></a>
    <?php if (in_array('prestamos.php', $permiso['paginas'], true)): ?><a href="prestamos.php"><?= t('navPrestamos') ?></a><?php endif; ?>
    <?php if (in_array('tickets.php', $permiso['paginas'], true)): ?><a href="tickets.php"><?= t('navTickets') ?></a><?php endif; ?>
    <?php if (in_array('reportes.php', $permiso['paginas'], true)): ?><a href="reportes.php"><?= t('navReportes') ?></a><?php endif; ?>
    <?php if (in_array('historial.php', $permiso['paginas'], true)): ?><a href="historial.php"><?= t('navHistorial') ?></a><?php endif; ?>

    <a href="logout.php" style="margin-left:auto;color:#fca5a5;"><?= t('navCerrarSesion') ?></a>
</nav>

<main>
    <section id="altaUsuarios">
        <h2><?= t('altaUsuariosTitulo') ?></h2>

        <form method="POST">
            <label for="cedulaUsuario"><?= t('labelCedula') ?></label>
            <input type="text" id="cedulaUsuario" name="cedula" placeholder="<?= t('placeholderCedula') ?>" maxlength="12" required>

            <label for="nombreUsuario"><?= t('labelNombreCompleto') ?></label>
            <input type="text" id="nombreUsuario" name="nombre" placeholder="<?= t('placeholderNombre') ?>" required>

            <label for="correoUsuario"><?= t('labelCorreo') ?></label>
            <input type="email" id="correoUsuario" name="correo" placeholder="<?= t('placeholderCorreo') ?>" required>

            <label for="rolUsuario"><?= t('labelRol') ?></label>
            <select id="rolUsuario" name="rol" required>
                <option value=""><?= t('opcionSeleccionarRol') ?></option>
                <option value="Solicitante"><?= tv('Solicitante') ?></option>
                <option value="Tecnico"><?= tv('Tecnico') ?></option>
                <option value="Administrador"><?= tv('Administrador') ?></option>
            </select>

            <button type="submit"><?= t('botonAgregarUsuario') ?></button>
        </form>

        <?php if ($mensaje !== ''): ?><p class="mensaje"><?= $mensaje ?></p><?php endif; ?>

        <div class="tabla-wrapper">
            <table>
                <thead>
                    <tr><th><?= t('thNum') ?></th><th><?= t('thCedula') ?></th><th><?= t('thNombre') ?></th><th><?= t('thCorreo') ?></th><th><?= t('thRol') ?></th><th><?= t('thAccionCol') ?></th></tr>
                </thead>
                <tbody>
                    <?php $i = 0; foreach ($resultado as $fila): $i++; ?>
                        <tr>
                            <td><?= $i ?></td>
                            <td><?= $fila['ci_usuario'] ?></td>
                            <td><?= $fila['nom'] ?> <?= $fila['ape'] ?></td>
                            <td><?= $fila['email'] ?></td>
                            <td><span class="badge badge-info"><?= tv($fila['rol']) ?></span></td>
                            <td><a class="btn-danger btn-sm" href="usuarios.php?eliminar=<?= $fila['ci_usuario'] ?>" onclick="return confirm('<?= t('confirmEliminarUsuario') ?>')"><?= t('botonEliminar') ?></a></td>
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
