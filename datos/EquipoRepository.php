<?php

declare(strict_types=1);

class EquipoRepository
{
    public function guardar(Equipo $equipo): bool
    {
        return Conexion::execute(
            'INSERT INTO equipo (estado, numeroSerie, modelo, marca, tipo) VALUES (?, ?, ?, ?, ?)',
            [
                $equipo->getEstado(),
                $equipo->getNumeroSerie(),
                $equipo->getModelo(),
                $equipo->getMarca(),
                $equipo->getTipo(),
            ]
        );
    }

    public function listar(): array
    {
        return Conexion::select('SELECT * FROM equipo ORDER BY id_equipo DESC');
    }

    public function listarDisponibles(): array
    {
        return Conexion::select("SELECT * FROM equipo WHERE estado = 'Disponible'");
    }

    public function cambiarEstado(int $idEquipo, string $estado): bool
    {
        return Conexion::execute('UPDATE equipo SET estado = ? WHERE id_equipo = ?', [$estado, $idEquipo]);
    }

    public function obtenerEstado(int $idEquipo): ?string
    {
        $valor = Conexion::scalar('SELECT estado FROM equipo WHERE id_equipo = ?', [$idEquipo]);
        return $valor === null ? null : (string)$valor;
    }

    public function contar(): int
    {
        return (int)Conexion::scalar('SELECT COUNT(*) AS n FROM equipo');
    }

    public function contarPorEstado(string $estado): int
    {
        return (int)Conexion::scalar('SELECT COUNT(*) AS n FROM equipo WHERE estado = ?', [$estado]);
    }
}
