<?php
session_start();
require "db.php";

$PERMISOS = [
    "administrador" => [
        "etiqueta" => "Administrador",
        "clase"    => "badge-danger",
        "paginas"  => ["index.php", "recursos.php", "usuarios.php", "prestamos.php", "tickets.php", "reportes.php", "historial.php", "perfil.php"],
        "inventarioEditar" => true, "usuariosAdministrar" => true, "ticketsCerrar" => true
    ],
    "tecnico" => [
        "etiqueta" => "Técnico",
        "clase"    => "badge-warning",
        "paginas"  => ["index.php", "recursos.php", "prestamos.php", "tickets.php", "reportes.php", "historial.php", "perfil.php"],
        "inventarioEditar" => false, "usuariosAdministrar" => false, "ticketsCerrar" => true
    ],
    "solicitante" => [
        "etiqueta" => "Solicitante",
        "clase"    => "badge-info",
        "paginas"  => ["index.php", "prestamos.php", "tickets.php", "perfil.php"],
        "inventarioEditar" => false, "usuariosAdministrar" => false, "ticketsCerrar" => false
    ]
];

function normalizarRol($rol) {
    $r = mb_strtolower(trim($rol));
    if (strpos($r, "admin") !== false) return "administrador";
    if (strpos($r, "tecnico") !== false || strpos($r, "técnico") !== false) return "tecnico";
    return "solicitante";
}

function validarCedula($cedula) {
    $ci = preg_replace("/[^0-9]/", "", $cedula);
    if (strlen($ci) !== 8) return false;
    $pesos = [2, 9, 8, 7, 6, 3, 4];
    $suma = 0;
    for ($i = 0; $i < 7; $i++) { $suma += (int)$ci[$i] * $pesos[$i]; }
    $verificador = (10 - $suma % 10) % 10;
    return $verificador === (int)$ci[7];
}

if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit;
}
$usuario = $_SESSION["usuario"];
$permiso = $PERMISOS[normalizarRol($usuario["rol"])];
if (!in_array("usuarios.php", $permiso["paginas"])) {
    header("Location: index.php");
    exit;
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $cedula = trim($_POST["cedula"]);
    $nombre = trim($_POST["nombre"]);
    $correo = trim($_POST["correo"]);
    $rol    = $_POST["rol"];

    if (!validarCedula($cedula)) {
        $mensaje = "La cédula no es válida (revisá el dígito verificador)";
    } else {
        $ci = (int)preg_replace("/[^0-9]/", "", $cedula);
        $partes = explode(" ", $nombre, 2);
        $nom = $partes[0];
        $ape = $partes[1] ?? "";
        $pass = password_hash("Default123!", PASSWORD_DEFAULT);

        $conn->execute_query("INSERT INTO usuario (ci_usuario, nom, ape, email, contrasena, rol)
                              VALUES ($ci, '$nom', '$ape', '$correo', '$pass', '$rol')");

        $mensaje = "Usuario agregado: $nombre";
    }
}

if (isset($_GET["eliminar"])) {
    $id = (int)$_GET["eliminar"];
    $conn->execute_query("DELETE FROM usuario WHERE ci_usuario = $id");
    $mensaje = "Usuario eliminado";
}

$resultado = $conn->execute_query("SELECT ci_usuario, nom, ape, email, rol FROM usuario ORDER BY ci_usuario DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGRSI – Usuarios</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header>
    <h1>SGRSI</h1>
    <p>Sistema de Gestión de Recursos y Soporte Informático</p>
    <div style="margin-top:8px;font-size:0.85rem;opacity:0.9;">
        <a href="perfil.php" style="color:inherit;text-decoration:none;"><?= $usuario["nom"] ?> <?= $usuario["ape"] ?></a>
        <span class="badge <?= $permiso["clase"] ?>" style="vertical-align:middle;margin-left:4px;"><?= $permiso["etiqueta"] ?></span>
    </div>
</header>

<nav>
    <a href="index.php">Inicio</a>
    <?php if (in_array("recursos.php", $permiso["paginas"])): ?><a href="recursos.php">Inventario</a><?php endif; ?>
    <a href="usuarios.php" class="activo">Usuarios</a>
    <?php if (in_array("prestamos.php", $permiso["paginas"])): ?><a href="prestamos.php">Préstamos</a><?php endif; ?>
    <?php if (in_array("tickets.php", $permiso["paginas"])): ?><a href="tickets.php">Tickets</a><?php endif; ?>
    <?php if (in_array("reportes.php", $permiso["paginas"])): ?><a href="reportes.php">Reportes</a><?php endif; ?>
    <?php if (in_array("historial.php", $permiso["paginas"])): ?><a href="historial.php">Historial</a><?php endif; ?>
    <a href="perfil.php">Mi perfil</a>
    <a href="logout.php" style="margin-left:auto;color:#fca5a5;">Cerrar sesión</a>
</nav>

<main>
    <section id="altaUsuarios">
        <h2>Alta de Usuarios</h2>

        <form method="POST">
            <label for="cedulaUsuario">Cédula de identidad</label>
            <input type="text" id="cedulaUsuario" name="cedula" placeholder="Ej: 3.814.725-8 o 38147258"
                   maxlength="12" required>

            <label for="nombreUsuario">Nombre completo</label>
            <input type="text" id="nombreUsuario" name="nombre" placeholder="Ej: María González" required>

            <label for="correoUsuario">Correo electrónico</label>
            <input type="email" id="correoUsuario" name="correo" placeholder="usuario@institucion.edu" required>

            <label for="rolUsuario">Rol</label>
            <select id="rolUsuario" name="rol" required>
                <option value="">Seleccionar rol…</option>
                <option value="Solicitante">Solicitante</option>
                <option value="Tecnico">Técnico</option>
                <option value="Administrador">Administrador</option>
            </select>

            <button type="submit">Agregar Usuario</button>
        </form>

        <?php if ($mensaje !== ""): ?><p class="mensaje"><?= $mensaje ?></p><?php endif; ?>

        <div class="tabla-wrapper">
            <table>
                <thead>
                    <tr><th>#</th><th>Cédula</th><th>Nombre</th><th>Correo</th><th>Rol</th><th>Acción</th></tr>
                </thead>
                <tbody>
                    <?php $i = 0; foreach ($resultado as $fila): $i++; ?>
                        <tr>
                            <td><?= $i ?></td>
                            <td><?= $fila["ci_usuario"] ?></td>
                            <td><?= $fila["nom"] ?> <?= $fila["ape"] ?></td>
                            <td><?= $fila["email"] ?></td>
                            <td><span class="badge badge-info"><?= $fila["rol"] ?></span></td>
                            <td><a class="btn-danger btn-sm" href="usuarios.php?eliminar=<?= $fila["ci_usuario"] ?>"
                                   onclick="return confirm('¿Eliminar este usuario?')">Eliminar</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<footer>
    <p>© SGRSI – Proyecto Bachillerato Tecnológico</p>
</footer>

</body>
</html>
