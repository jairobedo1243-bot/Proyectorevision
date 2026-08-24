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

if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit;
}
$usuario = $_SESSION["usuario"];
$permiso = $PERMISOS[normalizarRol($usuario["rol"])];
if (!in_array("recursos.php", $permiso["paginas"])) {
    header("Location: index.php");
    exit;
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && $permiso["inventarioEditar"]) {
    $numeroSerie = trim($_POST["numeroSerie"]);
    $modelo      = trim($_POST["modelo"]);
    $marca       = trim($_POST["marca"]);
    $tipo        = $_POST["tipo"];

    $conn->execute_query("INSERT INTO equipo (estado, numeroSerie, modelo, marca, tipo)
                          VALUES ('Disponible', '$numeroSerie', '$modelo', '$marca', '$tipo')");

    $mensaje = "Equipo agregado: $modelo";
}

$resultado = $conn->execute_query("SELECT * FROM equipo ORDER BY id_equipo DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGRSI – Inventario</title>
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
    <a href="recursos.php" class="activo">Inventario</a>
    <?php if (in_array("usuarios.php", $permiso["paginas"])): ?><a href="usuarios.php">Usuarios</a><?php endif; ?>
    <?php if (in_array("prestamos.php", $permiso["paginas"])): ?><a href="prestamos.php">Préstamos</a><?php endif; ?>
    <?php if (in_array("tickets.php", $permiso["paginas"])): ?><a href="tickets.php">Tickets</a><?php endif; ?>
    <?php if (in_array("reportes.php", $permiso["paginas"])): ?><a href="reportes.php">Reportes</a><?php endif; ?>
    <?php if (in_array("historial.php", $permiso["paginas"])): ?><a href="historial.php">Historial</a><?php endif; ?>
    <a href="perfil.php">Mi perfil</a>
    <a href="logout.php" style="margin-left:auto;color:#fca5a5;">Cerrar sesión</a>
</nav>

<main>
    <section id="registroEquipo">
        <h2>Registro de Equipos</h2>

        <?php if ($permiso["inventarioEditar"]): ?>
            <form method="POST">
                <label for="modeloEquipo">Modelo</label>
                <input type="text" id="modeloEquipo" name="modelo" placeholder="Ej: ProDesk 400" required>

                <label for="marcaEquipo">Marca</label>
                <input type="text" id="marcaEquipo" name="marca" placeholder="Ej: HP" required>

                <label for="numeroSerieEquipo">Número de serie</label>
                <input type="text" id="numeroSerieEquipo" name="numeroSerie" placeholder="Ej: SN-2024-0001" required>

                <label for="tipoEquipo">Tipo de equipo</label>
                <select id="tipoEquipo" name="tipo" required>
                    <option value="">Seleccionar…</option>
                    <option value="PC">PC</option>
                    <option value="Laptop">Laptop</option>
                    <option value="Monitor">Monitor</option>
                    <option value="Proyector">Proyector</option>
                    <option value="Television">Televisión</option>
                    <option value="Otro">Otro</option>
                </select>

                <button type="submit">Agregar Equipo</button>
            </form>
        <?php else: ?>
            <p class="mensaje info">Tu rol permite consultar el inventario, pero no registrar equipos nuevos.</p>
        <?php endif; ?>

        <?php if ($mensaje !== ""): ?><p class="mensaje"><?= $mensaje ?></p><?php endif; ?>

        <div class="tabla-wrapper">
            <table>
                <thead>
                    <tr><th>#</th><th>Modelo</th><th>Marca</th><th>N° Serie</th><th>Tipo</th><th>Estado</th></tr>
                </thead>
                <tbody>
                    <?php $i = 0; foreach ($resultado as $fila): $i++;
                        $badgeClase = $fila["estado"] === "Disponible" ? "badge-success"
                                    : ($fila["estado"] === "Prestado" ? "badge-warning" : "badge-danger");
                    ?>
                        <tr>
                            <td><?= $i ?></td>
                            <td><?= $fila["modelo"] ?></td>
                            <td><?= $fila["marca"] ?></td>
                            <td><?= $fila["numeroSerie"] ?></td>
                            <td><?= $fila["tipo"] ?></td>
                            <td><span class="badge <?= $badgeClase ?>"><?= $fila["estado"] ?></span></td>
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
