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
if (!in_array("prestamos.php", $permiso["paginas"])) {
    header("Location: index.php");
    exit;
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $ciSolicitante   = (int)$_POST["ciSolicitante"];
    $idEquipo        = (int)$_POST["idEquipo"];
    $fechaFinPrevista = $_POST["fechaFinPrevista"];
    $observacion     = trim($_POST["observacion"]);

    $conn->execute_query("INSERT INTO prestamo (fechaFinPrevista, observacion, estado, ci_solicitante, id_equipo)
                          VALUES ('$fechaFinPrevista', '$observacion', 'Entregado', $ciSolicitante, $idEquipo)");
    $conn->execute_query("UPDATE equipo SET estado = 'Prestado' WHERE id_equipo = $idEquipo");

    $mensaje = "Préstamo registrado";
}

if (isset($_GET["devolver"])) {
    $idPrestamo = (int)$_GET["devolver"];

    $conn->execute_query("UPDATE prestamo SET estado = 'Devuelto', fechaDevolucion = NOW() WHERE id_prestamo = $idPrestamo");
    $conn->execute_query("UPDATE equipo e JOIN prestamo p ON p.id_equipo = e.id_equipo
                          SET e.estado = 'Disponible' WHERE p.id_prestamo = $idPrestamo");
    $mensaje = "Equipo devuelto";
}

$prestamos = $conn->execute_query("SELECT p.*, u.nom, u.ape, e.modelo, e.marca, e.tipo
                                   FROM prestamo p
                                   JOIN usuario u ON p.ci_solicitante = u.ci_usuario
                                   JOIN equipo e ON p.id_equipo = e.id_equipo
                                   ORDER BY p.id_prestamo DESC");

$usuarios = $conn->execute_query("SELECT ci_usuario, nom, ape FROM usuario ORDER BY nom");
$equiposDisponibles = $conn->execute_query("SELECT * FROM equipo WHERE estado = 'Disponible'");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGRSI – Préstamos</title>
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
    <a href="prestamos.php" class="activo">Préstamos</a>
    <?php if (in_array("tickets.php", $permiso["paginas"])): ?><a href="tickets.php">Tickets</a><?php endif; ?>
    <?php if (in_array("reportes.php", $permiso["paginas"])): ?><a href="reportes.php">Reportes</a><?php endif; ?>
    <?php if (in_array("historial.php", $permiso["paginas"])): ?><a href="historial.php">Historial</a><?php endif; ?>
    <a href="perfil.php">Mi perfil</a>
    <a href="logout.php" style="margin-left:auto;color:#fca5a5;">Cerrar sesión</a>
</nav>

<main>
    <section id="prestamos">
        <h2>Registro de Préstamos</h2>

        <form method="POST">
            <label for="ciSolicitante">Solicitante</label>
            <select id="ciSolicitante" name="ciSolicitante" required>
                <option value="">Seleccionar solicitante…</option>
                <?php foreach ($usuarios as $u): ?>
                    <option value="<?= $u["ci_usuario"] ?>"><?= $u["nom"] ?> <?= $u["ape"] ?></option>
                <?php endforeach; ?>
            </select>

            <label for="idEquipo">Equipo</label>
            <select id="idEquipo" name="idEquipo" required>
                <option value="">Seleccionar equipo…</option>
                <?php foreach ($equiposDisponibles as $e): ?>
                    <option value="<?= $e["id_equipo"] ?>"><?= $e["modelo"] ?> (<?= $e["tipo"] ?>)</option>
                <?php endforeach; ?>
            </select>

            <label for="fechaFinPrevista">Fecha estimada de devolución</label>
            <input type="date" id="fechaFinPrevista" name="fechaFinPrevista" required>

            <label for="observacion">Observación</label>
            <input type="text" id="observacion" name="observacion" placeholder="Ej: Para trabajo en clase">

            <button type="submit">Registrar Préstamo</button>
        </form>

        <?php if ($mensaje !== ""): ?><p class="mensaje"><?= $mensaje ?></p><?php endif; ?>

        <div class="tabla-wrapper">
            <table>
                <thead>
                    <tr><th>#</th><th>Solicitante</th><th>Equipo</th><th>Tipo</th><th>Devolución</th><th>Estado</th><th>Acción</th></tr>
                </thead>
                <tbody>
                    <?php $i = 0; foreach ($prestamos as $p): $i++;
                        $prestado = $p["estado"] === "Entregado";
                    ?>
                        <tr>
                            <td><?= $i ?></td>
                            <td><?= $p["nom"] ?> <?= $p["ape"] ?></td>
                            <td><?= $p["modelo"] ?></td>
                            <td><?= $p["tipo"] ?></td>
                            <td><?= $p["fechaFinPrevista"] ?></td>
                            <td><span class="badge <?= $prestado ? "badge-warning" : "badge-success" ?>"><?= $p["estado"] ?></span></td>
                            <td>
                                <?php if ($prestado): ?>
                                    <a class="btn-sm btn-success" href="prestamos.php?devolver=<?= $p["id_prestamo"] ?>">Devolver</a>
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
