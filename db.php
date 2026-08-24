<?php

$db = "BD_SGRSI";
$host = "localhost";
$usuario = "root";
$clave = "1234";

$conn = new mysqli($host, $usuario, $clave, $db);
$conn->set_charset("utf8mb4");
