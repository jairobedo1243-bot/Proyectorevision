<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") { http_response_code(200); exit; }

require_once "conexion_db.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    $email = $data["email"] ?? "";
    $contrasena = $data["contrasena"] ?? "";

    if (!$email || !$contrasena) {
        http_response_code(400);
        echo json_encode(["error" => "Email y contraseña requeridos"]);
        exit;
    }

    $conn = conectar();
    $stmt = $conn->prepare("SELECT ci_usuario, nom, ape, email, contrasena, rol FROM usuario WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        http_response_code(401);
        echo json_encode(["error" => "Credenciales inválidas"]);
        $stmt->close();
        $conn->close();
        exit;
    }

    $user = $res->fetch_assoc();

    if (!password_verify($contrasena, $user["contrasena"])) {
        http_response_code(401);
        echo json_encode(["error" => "Credenciales inválidas"]);
        $stmt->close();
        $conn->close();
        exit;
    }

    $_SESSION["ci_usuario"] = $user["ci_usuario"];
    $_SESSION["nom"] = $user["nom"];
    $_SESSION["ape"] = $user["ape"];
    $_SESSION["email"] = $user["email"];
    $_SESSION["rol"] = $user["rol"];

    echo json_encode([
        "mensaje" => "Inicio de sesión exitoso",
        "usuario" => [
            "ci_usuario" => $user["ci_usuario"],
            "nom" => $user["nom"],
            "ape" => $user["ape"],
            "email" => $user["email"],
            "rol" => $user["rol"]
        ]
    ]);

    $stmt->close();
    $conn->close();
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if (isset($_GET["accion"]) && $_GET["accion"] === "cerrar") {
        session_destroy();
        echo json_encode(["mensaje" => "Sesión cerrada"]);
        exit;
    }

    if (isset($_SESSION["ci_usuario"])) {
        echo json_encode(["autenticado" => true, "usuario" => [
            "ci_usuario" => $_SESSION["ci_usuario"],
            "nom" => $_SESSION["nom"],
            "ape" => $_SESSION["ape"],
            "email" => $_SESSION["email"],
            "rol" => $_SESSION["rol"]
        ]]);
    } else {
        echo json_encode(["autenticado" => false]);
    }
    exit;
}

http_response_code(405);
echo json_encode(["error" => "Método no permitido"]);
?>