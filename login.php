<?php
session_start();
require "db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = $_POST["email"];
    $contrasena = $_POST["contrasena"];

    $resultado = $conn->execute_query("SELECT * FROM usuario WHERE email = '$email'");
    $usuario = $resultado->fetch_assoc();

    if (!$usuario || !password_verify($contrasena, $usuario["contrasena"])) {
        $error = "Credenciales inválidas";
    } else {
        $_SESSION["usuario"] = [
            "ci_usuario" => $usuario["ci_usuario"],
            "nom"        => $usuario["nom"],
            "ape"        => $usuario["ape"],
            "email"      => $usuario["email"],
            "rol"        => $usuario["rol"]
        ];
        header("Location: index.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGRSI – Iniciar Sesión</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="login.css">
</head>
<body>

<header>
    <h1>SGRSI</h1>
    <p>Sistema de Gestión de Recursos y Soporte Informático</p>
</header>

<div class="login-wrapper">
    <div class="login-card">
        <h2>Iniciar Sesión</h2>
        <p class="sub">Ingresá tus credenciales para acceder al sistema</p>

        <div class="login-error" style="display:<?= $error !== "" ? "block" : "none" ?>"><?= $error ?></div>

        <form method="POST">
            <label for="email">Correo electrónico</label>
            <input type="email" id="email" name="email" placeholder="ejemplo@utu.edu.uy" required>

            <label for="contrasena">Contraseña</label>

            <div style="position:relative;">
                <input type="password" id="contrasena" name="contrasena" required
                       style="padding-right:44px;">

                <button type="button" id="btnVerPass"
                        aria-label="Mostrar contraseña"
                        title="Mostrar contraseña"
                        style="position:absolute; top:0; right:0; width:42px;
                               height:calc(100% - 18px); padding:0; margin:0;
                               background:none; border:none; cursor:pointer;
                               display:flex; align-items:center; justify-content:center;
                               color:#6b7280;">

                    <svg id="iconoOjoAbierto" width="20" height="20" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>

                    <svg id="iconoOjoCerrado" width="20" height="20" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round"
                         style="display:none;">
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"></path>
                        <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"></path>
                        <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"></path>
                        <line x1="1" y1="1" x2="23" y2="23"></line>
                    </svg>
                </button>
            </div>

            <button type="submit">Ingresar</button>
        </form>

        <div class="info">
            <strong>Usuarios de prueba:</strong><br>
            Admin: <code>juan.perez@utu.edu.uy</code> / <code>Perez123!</code><br>
            Soporte: <code>ana.martinez@utu.edu.uy</code> / <code>Marti234!</code><br>
            Docente: <code>maria.garcia@utu.edu.uy</code> / <code>Garcia456!</code><br>
            Usuarios nuevos: <code>Default123!</code>
        </div>
    </div>
</div>

<footer>
    <p>© SGRSI – Proyecto Bachillerato Tecnológico</p>
</footer>

<script>
    const inputPass  = document.getElementById("contrasena");
    const btnVerPass = document.getElementById("btnVerPass");
    const ojoAbierto = document.getElementById("iconoOjoAbierto");
    const ojoCerrado = document.getElementById("iconoOjoCerrado");

    btnVerPass.addEventListener("click", function () {
        const estaOculta = inputPass.type === "password";
        inputPass.type = estaOculta ? "text" : "password";
        ojoAbierto.style.display = estaOculta ? "none" : "block";
        ojoCerrado.style.display = estaOculta ? "block" : "none";
        inputPass.focus();
    });
</script>
</body>
</html>
