<?php

declare(strict_types=1);
defined('ENTRYPOINT') || (http_response_code(404) && exit);

/**
 * Pone al día la estructura de una base ya instalada para la baja lógica (RF-06 / RN-10).
 * Lo usa public/migrar.php (una sola vez). Cada paso revisa si hace falta,
 * así se puede ejecutar varias veces sin romper nada.
 */
class MigracionRepository
{
    /** Columnas que guardan la cédula de una persona y deben apuntar a usuario. */
    private const REFERENCIAS = [
        ['tabla' => 'prestamo', 'columna' => 'ci_solicitante'],
        ['tabla' => 'ticket', 'columna' => 'ci_solicitante'],
        ['tabla' => 'servicio', 'columna' => 'ci_usuario'],
    ];

    /**
     * Busca un usuario por correo SIN filtrar por la columna activo (que puede no existir
     * todavía). Solo la usa migrar.php para identificar al Administrador.
     */
    public function buscarUsuarioPorEmail(string $email): ?array
    {
        return Conexion::selectOne(
            'SELECT ci_usuario, nom, ape, email, contrasena, rol FROM usuario WHERE email = ?',
            [$email]
        );
    }

    /** Lista de pasos pendientes (texto para mostrar). Vacía si la base ya está al día. */
    public function pendientes(): array
    {
        $pasos = [];

        if (!$this->existeColumnaActivo()) {
            $pasos[] = 'Agregar la columna usuario.activo (baja lógica)';
        }

        if (!$this->existeColumna('debe_cambiar_clave')) {
            $pasos[] = 'Agregar la columna usuario.debe_cambiar_clave (cambio obligatorio de la clave inicial)';
        }

        $huerfanos = $this->cedulasHuerfanas();
        if ($huerfanos !== []) {
            $pasos[] = 'Recuperar ' . count($huerfanos) . ' cédula(s) con registros pero sin usuario, como usuarios dados de baja: ' . implode(', ', $huerfanos);
        }

        foreach (self::REFERENCIAS as $ref) {
            if (!$this->tieneClaveForanea($ref['tabla'], $ref['columna'])) {
                $pasos[] = "Agregar la clave foránea {$ref['tabla']}.{$ref['columna']} → usuario.ci_usuario";
            }
        }

        return $pasos;
    }

    /** Aplica los pasos pendientes en orden. Devuelve los que fallaron (vacío si todo salió bien). */
    public function aplicar(): array
    {
        $fallas = [];

        if (!$this->existeColumnaActivo()
            && Conexion::filasAfectadas('ALTER TABLE usuario ADD COLUMN activo TINYINT(1) NOT NULL DEFAULT 1') < 0) {
            return ['columna activo'];
        }

        // Los usuarios que ya existían conservan su clave: quedan con debe_cambiar_clave = 0.
        if (!$this->existeColumna('debe_cambiar_clave')
            && Conexion::filasAfectadas('ALTER TABLE usuario ADD COLUMN debe_cambiar_clave TINYINT(1) NOT NULL DEFAULT 0') < 0) {
            return ['columna debe_cambiar_clave'];
        }

        // Si en algún momento se borraron usuarios que tenían registros, se recrean como
        // usuarios dados de baja (sin contraseña válida) para que la integridad vuelva a cumplirse.
        foreach ($this->cedulasHuerfanas() as $ci) {
            $ok = Conexion::filasAfectadas(
                "INSERT INTO usuario (ci_usuario, nom, ape, email, contrasena, rol, activo)
                 VALUES (?, 'Usuario', 'sin datos', ?, '!', 'Solicitante', 0)",
                [$ci, 'baja.' . $ci . '@sgrsi.invalid']
            ) === 1;
            if (!$ok) {
                $fallas[] = "usuario $ci";
            }
        }

        foreach (self::REFERENCIAS as $ref) {
            if ($this->tieneClaveForanea($ref['tabla'], $ref['columna'])) {
                continue;
            }
            $sql = "ALTER TABLE `{$ref['tabla']}` ADD CONSTRAINT `fk_{$ref['tabla']}_usuario`
                    FOREIGN KEY (`{$ref['columna']}`) REFERENCES usuario(ci_usuario)";
            if (Conexion::filasAfectadas($sql) < 0) {
                $fallas[] = "{$ref['tabla']}.{$ref['columna']}";
            }
        }

        return $fallas;
    }

    private function existeColumnaActivo(): bool
    {
        return $this->existeColumna('activo');
    }

    private function existeColumna(string $columna): bool
    {
        return (int)Conexion::scalar(
            "SELECT COUNT(*) FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuario' AND COLUMN_NAME = ?",
            [$columna]
        ) > 0;
    }

    private function tieneClaveForanea(string $tabla, string $columna): bool
    {
        return (int)Conexion::scalar(
            "SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?
               AND REFERENCED_TABLE_NAME = 'usuario'",
            [$tabla, $columna]
        ) > 0;
    }

    /** Cédulas usadas en préstamos, tickets o servicios que no existen en usuario. */
    private function cedulasHuerfanas(): array
    {
        $filas = Conexion::select(
            'SELECT ci FROM (
                SELECT ci_solicitante AS ci FROM prestamo
                UNION SELECT ci_solicitante FROM ticket
                UNION SELECT ci_usuario FROM servicio
             ) r
             WHERE ci NOT IN (SELECT ci_usuario FROM usuario)
             ORDER BY ci'
        );

        return array_map(static fn(array $f): int => (int)$f['ci'], $filas);
    }
}
