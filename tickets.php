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
if (!in_array("tickets.php", $permiso["paginas"])) {
    header("Location: index.php");
    exit;
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $ciSolicitante = (int)$_POST["ciSolicitante"];
    $prioridad     = $_POST["prioridad"];
    $titulo        = trim($_POST["titulo"]);
    $descripcion   = trim($_POST["descripcion"]);

    $conn->execute_query("INSERT INTO ticket (titulo, descripcion, ci_solicitante, prioridad)
                          VALUES ('$titulo', '$descripcion', $ciSolicitante, '$prioridad')");

    $mensaje = "Ticket creado correctamente";
}

if (isset($_GET["cerrar"]) && $permiso["ticketsCerrar"]) {
    $id = (int)$_GET["cerrar"];
    $conn->execute_query("UPDATE ticket SET estado = 'Cerrado', fechaFin = NOW() WHERE id_ticket = $id");
    $mensaje = "Ticket cerrado";
}

$tickets = $conn->execute_query("SELECT t.*, u.nom, u.ape
                                 FROM ticket t
                                 JOIN usuario u ON t.ci_solicitante = u.ci_usuario
                                 ORDER BY t.id_ticket DESC");

$usuarios = $conn->execute_query("SELECT ci_usuario, nom, ape FROM usuario ORDER BY nom");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGRSI – Tickets</title>
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
    <a href="tickets.php" class="activo">Tickets</a>
    <?php if (in_array("reportes.php", $permiso["paginas"])): ?><a href="reportes.php">Reportes</a><?php endif; ?>
    <?php if (in_array("historial.php", $permiso["paginas"])): ?><a href="historial.php">Historial</a><?php endif; ?>
    <a href="perfil.php">Mi perfil</a>
    <a href="logout.php" style="margin-left:auto;color:#fca5a5;">Cerrar sesión</a>
</nav>

<main>
    <section id="tickets">
        <h2>Mesa de Ayuda – Tickets</h2>

        <form method="POST">
            <label for="ciSolicitante">Solicitante</label>
            <select id="ciSolicitante" name="ciSolicitante" required>
                <option value="">Seleccionar solicitante…</option>
                <?php foreach ($usuarios as $u): ?>
                    <option value="<?= $u["ci_usuario"] ?>"><?= $u["nom"] ?> <?= $u["ape"] ?></option>
                <?php endforeach; ?>
            </select>

            <label for="titulo">Título</label>
            <input type="text" id="titulo" name="titulo" placeholder="Ej: PC no enciende" required>

            <label for="prioridad">Prioridad</label>
            <select id="prioridad" name="prioridad" required>
                <option value="">Seleccionar…</option>
                <option value="Alta">Alta</option>
                <option value="Media">Media</option>
                <option value="Baja">Baja</option>
            </select>

            <label for="descripcion">Descripción del problema</label>
            <textarea id="descripcion" name="descripcion" placeholder="Describí el problema con detalle…" required></textarea>

            <button type="submit">Crear Ticket</button>
        </form>

        <?php if ($mensaje !== ""): ?><p class="mensaje"><?= $mensaje ?></p><?php endif; ?>

        <div class="tabla-wrapper">
            <table>
                <thead>
                    <tr><th>#</th><th>Fecha</th><th>Solicitante</th><th>Título</th><th>Prioridad</th><th>Problema</th><th>Estado</th><th>Acción</th></tr>
                </thead>
                <tbody>
                    <?php $i = 0; foreach ($tickets as $t): $i++;
                        $abierto = $t["estado"] !== "Cerrado";
                        $prioClase = $t["prioridad"] === "Alta" ? "badge-danger" : ($t["prioridad"] === "Media" ? "badge-warning" : "badge-info");
                    ?>
                        <tr>
                            <td><?= $i ?></td>
                            <td><?= $t["fechaCreacion"] ?></td>
                            <td><?= $t["nom"] ?> <?= $t["ape"] ?></td>
                            <td><?= $t["titulo"] ?></td>
                            <td><span class="badge <?= $prioClase ?>"><?= $t["prioridad"] ?></span></td>
                            <td><?= $t["descripcion"] ?></td>
                            <td><span class="badge <?= $abierto ? "badge-warning" : "badge-success" ?>"><?= $t["estado"] ?></span></td>
                            <td>
                                <?php if ($abierto && $permiso["ticketsCerrar"]): ?>
                                    <a class="btn-sm btn-success" href="tickets.php?cerrar=<?= $t["id_ticket"] ?>">Cerrar</a>
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
    <p>© SGRSI – Proyecto Bachillerato Tecnológico</p>
</footer>

</body>
</html>
