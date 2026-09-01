<?php

declare(strict_types=1);

class Database
{
    private static ?mysqli $connection = null;

    public static function getConnection(): mysqli
    {
        if (self::$connection instanceof mysqli) {
            return self::$connection;
        }

        $db = 'BD_SGRSI';
        $host = 'localhost';
        $usuario = 'root';
        $clave = '1234';

        $connection = new mysqli($host, $usuario, $clave, $db);
        if ($connection->connect_error) {
            throw new RuntimeException('Error de conexión: ' . $connection->connect_error);
        }

        $connection->set_charset('utf8mb4');
        self::$connection = $connection;

        return self::$connection;
    }

    public static function select(string $sql, array $params = []): array
    {
        $stmt = self::prepare($sql, $params);
        if ($stmt === null) {
            return [];
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();

        return $rows;
    }

    public static function selectOne(string $sql, array $params = []): ?array
    {
        $stmt = self::prepare($sql, $params);
        if ($stmt === null) {
            return null;
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result ? $result->fetch_assoc() : null;
        $stmt->close();

        return $row ?: null;
    }

    public static function scalar(string $sql, array $params = []): mixed
    {
        $row = self::selectOne($sql, $params);
        if ($row === null) {
            return null;
        }

        return reset($row);
    }

    public static function execute(string $sql, array $params = []): bool
    {
        $stmt = self::prepare($sql, $params);
        if ($stmt === null) {
            return false;
        }

        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    private static function prepare(string $sql, array $params = []): ?mysqli_stmt
    {
        $statement = self::getConnection()->prepare($sql);
        if ($statement === false) {
            return null;
        }

        if ($params !== []) {
            $types = str_repeat('s', count($params));
            $statement->bind_param($types, ...$params);
        }

        return $statement;
    }
}
