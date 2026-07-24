<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: GET, POST, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

require_once "conexion_db.php";

$conn = conectar();
$method = $_SERVER["REQUEST_METHOD"];

switch ($method) {
    case "GET":
        $sql = "SELECT ci_usuario, nom, ape, email, rol FROM usuario ORDER BY ci_usuario DESC";
        $res = $conn->query($sql);
        $usuarios = [];
        while ($row = $res->fetch_assoc()) {
            $usuarios[] = $row;
        }
        echo json_encode($usuarios);
        break;

    case "POST":
        $data = json_decode(file_get_contents("php://input"), true);
        $nombre = $data["nombre"] ?? "";
        $correo = $data["correo"] ?? "";
        $rol = $data["rol"] ?? "";
        
        $pass = password_hash("default123", PASSWORD_DEFAULT);

        if (!$nombre || !$correo || !$rol) {
            http_response_code(400);
            echo json_encode(["error" => "Faltan campos obligatorios"]);
            break;
        }

        $stmt = $conn->prepare("INSERT INTO usuario (nom, ape, email, contrasena, rol) VALUES (?, '', ?, ?, ?)");
        $stmt->bind_param("ssss", $nombre, $correo, $pass, $rol);

        if ($stmt->execute()) {
            echo json_encode(["mensaje" => "Usuario creado", "id" => $stmt->insert_id]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => $conn->error]);
        }
        $stmt->close();
        break;

    case "DELETE":
        $id = $_GET["id"] ?? 0;
        $stmt = $conn->prepare("DELETE FROM usuario WHERE ci_usuario = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode(["mensaje" => "Usuario eliminado"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => $conn->error]);
        }
        $stmt->close();
        break;
}

$conn->close();
