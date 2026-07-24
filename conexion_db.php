<?php
$server = "localhost";
$user = "root";
$pass = "";
$bd = "BD_SGRSI";

function conectar() {
    global $server, $user, $pass, $bd;
    $conn = new mysqli($server, $user, $pass, $bd);
    if ($conn->connect_error) {
        die(json_encode(["error" => "Conexión fallida: " . $conn->connect_error]));
    }
    $conn->set_charset("utf8");
    return $conn;
}
