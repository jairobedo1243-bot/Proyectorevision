<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$auth = new SessionAuth();
$usuario = $auth->requireLogin();
$permiso = $auth->requirePageAccess('usuarios.php', $usuario);
$idioma = Translator::currentLanguage();
$mensaje = '';
$exito = false;

$repo = new UsuarioRepository();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valido() || !$permiso['usuariosAdministrar']) {
        $mensaje = 'Solicitud no válida, recargá la página';
    } elseif (isset($_POST['eliminar'])) {
        $ci = (int)$_POST['eliminar'];
        if ($ci === (int)$usuario['ci_usuario']) {
            $mensaje = 'No podés eliminar tu propio usuario';
        } elseif ($repo->eliminar($ci)) {
            $mensaje = t('msgUsuarioEliminado');
            $exito = true;
        } else {
            $mensaje = 'No se pudo eliminar (el usuario tiene préstamos o tickets)';
        }
    } else {
        $correo = trim((string)($_POST['correo'] ?? ''));
        $rol = (string)($_POST['rol'] ?? '');
        $nuevo = Usuario::crearDesdeFormulario(
            trim((string)($_POST['cedula'] ?? '')),
            trim((string)($_POST['nombre'] ?? '')),
            $correo,
            $rol
        );

        if ($nuevo === null) {
            $mensaje = t('msgCedulaInvalida');
        } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL) || strlen($correo) > 80) {
            $mensaje = 'El correo no es válido';
        } elseif (!in_array($rol, ['Solicitante', 'Tecnico', 'Administrador'], true)) {
            $mensaje = 'El rol no es válido';
        } elseif (!$repo->guardar($nuevo)) {
            $mensaje = 'No se pudo agregar (la cédula o el correo ya existen)';
        } else {
            $mensaje = t('msgUsuarioAgregado') . $nuevo->getNom() . ' ' . $nuevo->getApe()
                . ' — Contraseña inicial: ' . $nuevo->getContrasenaInicial() . ' (anotala, no se vuelve a mostrar)';
            $exito = true;
        }
    }
}

$resultado = $repo->listar();
?>
<!DOCTYPE html>
<html lang="<?= $idioma ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('tituloUsuarios') ?></title>
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
            <?= csrf_campo() ?>
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

        <?php if ($mensaje !== ''): ?><p class="mensaje <?= $exito ? 'exito' : 'error' ?>"><?= e($mensaje) ?></p><?php endif; ?>

        <div class="tabla-wrapper">
            <table>
                <thead>
                    <tr><th><?= t('thNum') ?></th><th><?= t('thCedula') ?></th><th><?= t('thNombre') ?></th><th><?= t('thCorreo') ?></th><th><?= t('thRol') ?></th><th><?= t('thAccionCol') ?></th></tr>
                </thead>
                <tbody>
                    <?php $i = 0; foreach ($resultado as $fila): $i++; ?>
                        <tr>
                            <td><?= $i ?></td>
                            <td><?= e($fila['ci_usuario']) ?></td>
                            <td><?= e($fila['nom']) ?> <?= e($fila['ape']) ?></td>
                            <td><?= e($fila['email']) ?></td>
                            <td><span class="badge badge-info"><?= tv($fila['rol']) ?></span></td>
                            <td><form method="post" style="display:inline" onsubmit="return confirm('<?= t('confirmEliminarUsuario') ?>')">
                                    <?= csrf_campo() ?>
                                    <input type="hidden" name="eliminar" value="<?= e($fila['ci_usuario']) ?>">
                                    <button type="submit" class="btn-danger btn-sm"><?= t('botonEliminar') ?></button>
                                </form></td>
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
