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
$rolPropio = normalizarRol($usuario["rol"]);
$permiso = $PERMISOS[$rolPropio];

$MODULOS = [
    ["archivo" => "index.php",      "nombre" => "Inicio"],
    ["archivo" => "recursos.php",   "nombre" => "Inventario"],
    ["archivo" => "usuarios.php",   "nombre" => "Usuarios"],
    ["archivo" => "prestamos.php",  "nombre" => "Préstamos"],
    ["archivo" => "tickets.php",    "nombre" => "Tickets"],
    ["archivo" => "reportes.php",   "nombre" => "Reportes"],
    ["archivo" => "historial.php",  "nombre" => "Historial"]
];

$ACCIONES = [
    ["clave" => "inventarioEditar",    "nombre" => "Dar de alta equipos en el inventario"],
    ["clave" => "usuariosAdministrar", "nombre" => "Crear y eliminar usuarios"],
    ["clave" => "ticketsCerrar",       "nombre" => "Cerrar tickets de soporte"]
];

$ci = str_pad((string)$usuario["ci_usuario"], 8, "0", STR_PAD_LEFT);
$cedulaFormateada = $ci[0] . "." . substr($ci, 1, 3) . "." . substr($ci, 4, 3) . "-" . $ci[7];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGRSI – Mi Perfil</title>
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
    <?php if (in_array("reportes.php", $permiso["paginas"])): ?><a href="reportes.php">Reportes</a><?php endif; ?>
    <?php if (in_array("historial.php", $permiso["paginas"])): ?><a href="historial.php">Historial</a><?php endif; ?>
    <a href="perfil.php" class="activo">Mi perfil</a>
    <a href="logout.php" style="margin-left:auto;color:#fca5a5;">Cerrar sesión</a>
</nav>

<main>
    <section id="perfil">
        <h2>Mi Perfil</h2>

        <div class="tabla-wrapper">
            <table>
                <tbody>
                    <tr><td style="width:35%"><strong>Nombre</strong></td><td><?= $usuario["nom"] ?> <?= $usuario["ape"] ?></td></tr>
                    <tr><td><strong>Email</strong></td><td><?= $usuario["email"] ?></td></tr>
                    <tr><td><strong>Cédula</strong></td><td><?= $cedulaFormateada ?></td></tr>
                    <tr><td><strong>Rol en la base</strong></td><td><?= $usuario["rol"] ?> <span class="badge <?= $permiso["clase"] ?>"><?= $permiso["etiqueta"] ?></span></td></tr>
                </tbody>
            </table>
        </div>

        <h2 style="margin-top:28px;">Rol y permisos</h2>
        <p style="color:var(--text-muted);font-size:0.9rem;margin-bottom:14px;">
            Tu rol determina a qué módulos podés entrar y qué acciones podés realizar
            dentro de cada uno.
        </p>

        <div class="stats-grid" style="grid-template-columns:repeat(auto-fit,minmax(260px,1fr));">
            <div class="stat-card" style="border-top-color:var(--success);text-align:left;padding:16px 18px;">
                <h3 style="color:var(--success);margin-bottom:10px;font-size:1rem;">Podés hacer</h3>
                <ul style="margin:0;padding-left:18px;font-size:0.9rem;line-height:1.7;">
                    <?php foreach ($MODULOS as $m): if (in_array($m["archivo"], $permiso["paginas"])): ?>
                        <li>✓ Entrar al módulo <?= $m["nombre"] ?></li>
                    <?php endif; endforeach; ?>
                    <?php foreach ($ACCIONES as $a): if ($permiso[$a["clave"]]): ?>
                        <li>✓ <?= $a["nombre"] ?></li>
                    <?php endif; endforeach; ?>
                </ul>
            </div>
            <div class="stat-card" style="border-top-color:var(--danger);text-align:left;padding:16px 18px;">
                <h3 style="color:var(--danger);margin-bottom:10px;font-size:1rem;">No podés hacer</h3>
                <ul style="margin:0;padding-left:18px;font-size:0.9rem;line-height:1.7;">
                    <?php foreach ($MODULOS as $m): if (!in_array($m["archivo"], $permiso["paginas"])): ?>
                        <li>✕ Entrar al módulo <?= $m["nombre"] ?></li>
                    <?php endif; endforeach; ?>
                    <?php foreach ($ACCIONES as $a): if (!$permiso[$a["clave"]]): ?>
                        <li>✕ <?= $a["nombre"] ?></li>
                    <?php endif; endforeach; ?>
                </ul>
            </div>
        </div>

        <h2 style="margin-top:28px;">Comparación de roles</h2>
        <div class="tabla-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Módulo o acción</th>
                        <?php foreach (["administrador", "tecnico", "solicitante"] as $r): ?>
                            <th style="text-align:center"><?= $PERMISOS[$r]["etiqueta"] ?><?= $r === $rolPropio ? " ←" : "" ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($MODULOS as $m): ?>
                        <tr>
                            <td><?= $m["nombre"] ?></td>
                            <?php foreach (["administrador", "tecnico", "solicitante"] as $r):
                                $puede = in_array($m["archivo"], $PERMISOS[$r]["paginas"]); ?>
                                <td style="text-align:center"><span class="badge <?= $puede ? "badge-success" : "badge-danger" ?>"><?= $puede ? "Sí" : "No" ?></span></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                    <?php foreach ($ACCIONES as $a): ?>
                        <tr>
                            <td><?= $a["nombre"] ?></td>
                            <?php foreach (["administrador", "tecnico", "solicitante"] as $r):
                                $puede = $PERMISOS[$r][$a["clave"]]; ?>
                                <td style="text-align:center"><span class="badge <?= $puede ? "badge-success" : "badge-danger" ?>"><?= $puede ? "Sí" : "No" ?></span></td>
                            <?php endforeach; ?>
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
