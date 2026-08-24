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
if (!in_array("reportes.php", $permiso["paginas"])) {
    header("Location: index.php");
    exit;
}

$equiposTotal      = $conn->execute_query("SELECT COUNT(*) AS n FROM equipo")->fetch_assoc()["n"];
$equiposDisponibles = $conn->execute_query("SELECT COUNT(*) AS n FROM equipo WHERE estado = 'Disponible'")->fetch_assoc()["n"];
$equiposPrestados  = $conn->execute_query("SELECT COUNT(*) AS n FROM equipo WHERE estado = 'Prestado'")->fetch_assoc()["n"];

$ticketsAbiertos = $conn->execute_query("SELECT COUNT(*) AS n FROM ticket WHERE estado != 'Cerrado'")->fetch_assoc()["n"];
$ticketsCerrados = $conn->execute_query("SELECT COUNT(*) AS n FROM ticket WHERE estado = 'Cerrado'")->fetch_assoc()["n"];

$prestamosTotal     = $conn->execute_query("SELECT COUNT(*) AS n FROM prestamo")->fetch_assoc()["n"];
$prestamosActivos   = $conn->execute_query("SELECT COUNT(*) AS n FROM prestamo WHERE estado = 'Entregado'")->fetch_assoc()["n"];
$prestamosDevueltos = $conn->execute_query("SELECT COUNT(*) AS n FROM prestamo WHERE estado = 'Devuelto'")->fetch_assoc()["n"];

$fecha = date("d/m/Y");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGRSI – Reportes</title>
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
    <?php if (in_array("usuarios.php", $permiso["paginas"])): ?><a href="usuarios.php">Usuarios</a><?php endif; ?>
    <?php if (in_array("prestamos.php", $permiso["paginas"])): ?><a href="prestamos.php">Préstamos</a><?php endif; ?>
    <?php if (in_array("tickets.php", $permiso["paginas"])): ?><a href="tickets.php">Tickets</a><?php endif; ?>
    <a href="reportes.php" class="activo">Reportes</a>
    <?php if (in_array("historial.php", $permiso["paginas"])): ?><a href="historial.php">Historial</a><?php endif; ?>
    <a href="perfil.php">Mi perfil</a>
    <a href="logout.php" style="margin-left:auto;color:#fca5a5;">Cerrar sesión</a>
</nav>

<main>
    <section id="reportes">
        <h2>Reportes del Sistema</h2>
        <p style="color: var(--text-muted); margin-bottom: 16px; font-size:0.92rem;">
            Resumen del estado actual del inventario, préstamos y tickets.
        </p>

        <div class="stats-grid">
            <div class="stat-card" style="border-top-color:#1e3a5f">
                <div class="numero" style="color:#1e3a5f"><?= $equiposTotal ?></div>
                <div class="etiqueta">Equipos totales</div>
            </div>
            <div class="stat-card" style="border-top-color:#16a34a">
                <div class="numero" style="color:#16a34a"><?= $equiposDisponibles ?></div>
                <div class="etiqueta">Disponibles</div>
            </div>
            <div class="stat-card" style="border-top-color:#d97706">
                <div class="numero" style="color:#d97706"><?= $equiposPrestados ?></div>
                <div class="etiqueta">Prestados</div>
            </div>
            <div class="stat-card" style="border-top-color:#dc2626">
                <div class="numero" style="color:#dc2626"><?= $ticketsAbiertos ?></div>
                <div class="etiqueta">Tickets abiertos</div>
            </div>
            <div class="stat-card" style="border-top-color:#16a34a">
                <div class="numero" style="color:#16a34a"><?= $ticketsCerrados ?></div>
                <div class="etiqueta">Tickets cerrados</div>
            </div>
            <div class="stat-card" style="border-top-color:#d97706">
                <div class="numero" style="color:#d97706"><?= $prestamosActivos ?></div>
                <div class="etiqueta">Préstamos activos</div>
            </div>
        </div>

        <div class="tabla-wrapper">
            <table>
                <thead><tr><th>Concepto</th><th>Valor</th></tr></thead>
                <tbody>
                    <tr><td>Total de equipos</td><td><strong><?= $equiposTotal ?></strong></td></tr>
                    <tr><td>Equipos disponibles</td><td><strong><?= $equiposDisponibles ?></strong></td></tr>
                    <tr><td>Equipos prestados</td><td><strong><?= $equiposPrestados ?></strong></td></tr>
                    <tr><td>Tickets abiertos</td><td><strong><?= $ticketsAbiertos ?></strong></td></tr>
                    <tr><td>Tickets cerrados</td><td><strong><?= $ticketsCerrados ?></strong></td></tr>
                    <tr><td>Préstamos activos</td><td><strong><?= $prestamosActivos ?></strong></td></tr>
                    <tr><td>Préstamos devueltos</td><td><strong><?= $prestamosDevueltos ?></strong></td></tr>
                </tbody>
            </table>
        </div>
        <p style="margin-top:12px;font-size:0.8rem;color:var(--text-muted)">Reporte generado el <?= $fecha ?></p>
    </section>
</main>

<footer>
    <p>© SGRSI – Proyecto Bachillerato Tecnológico</p>
</footer>

</body>
</html>
