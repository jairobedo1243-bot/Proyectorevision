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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGRSI – Inicio</title>
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
    <a href="index.php" class="activo">Inicio</a>
    <?php if (in_array("recursos.php", $permiso["paginas"])): ?><a href="recursos.php">Inventario</a><?php endif; ?>
    <?php if (in_array("usuarios.php", $permiso["paginas"])): ?><a href="usuarios.php">Usuarios</a><?php endif; ?>
    <?php if (in_array("prestamos.php", $permiso["paginas"])): ?><a href="prestamos.php">Préstamos</a><?php endif; ?>
    <?php if (in_array("tickets.php", $permiso["paginas"])): ?><a href="tickets.php">Tickets</a><?php endif; ?>
    <?php if (in_array("reportes.php", $permiso["paginas"])): ?><a href="reportes.php">Reportes</a><?php endif; ?>
    <?php if (in_array("historial.php", $permiso["paginas"])): ?><a href="historial.php">Historial</a><?php endif; ?>
    <a href="perfil.php">Mi perfil</a>
    <a href="logout.php" style="margin-left:auto;color:#fca5a5;">Cerrar sesión</a>
</nav>

<main>
    <div class="bienvenida-hero">
        <h2>Bienvenido a SGRSI</h2>
        <p>Gestioná equipos, usuarios, préstamos y tickets de soporte desde un solo lugar.
           Usá el menú de arriba o accedé directamente a cada módulo.</p>
    </div>

    <div class="modulos-grid">
        <a href="recursos.php" class="modulo-card">
            <div class="icono"></div>
            <h3>Inventario</h3>
            <p>Registrá y consultá los equipos disponibles.</p>
        </a>
        <a href="usuarios.php" class="modulo-card">
            <div class="icono"></div>
            <h3>Usuarios</h3>
            <p>Alta de solicitantes, técnicos y administradores.</p>
        </a>
        <a href="prestamos.php" class="modulo-card">
            <div class="icono"></div>
            <h3>Préstamos</h3>
            <p>Registrá préstamos y devoluciones de equipos.</p>
        </a>
        <a href="tickets.php" class="modulo-card">
            <div class="icono"></div>
            <h3>Tickets</h3>
            <p>Mesa de ayuda: abrí y cerrá tickets de soporte.</p>
        </a>
        <a href="reportes.php" class="modulo-card">
            <div class="icono"></div>
            <h3>Reportes</h3>
            <p>Resumen del estado del sistema en tiempo real.</p>
        </a>
        <a href="historial.php" class="modulo-card">
            <div class="icono"></div>
            <h3>Historial</h3>
            <p>Revisá el registro de tickets y reportes anteriores.</p>
        </a>
    </div>
</main>

<footer>
    <p>© SGRSI – Proyecto Bachillerato Tecnológico</p>
</footer>

</body>
</html>
