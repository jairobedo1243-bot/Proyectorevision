<?php

declare(strict_types=1);
defined('ENTRYPOINT') || (http_response_code(404) && exit);
class PrestamoRepository
{
    public function guardar(Prestamo $prestamo): bool
    {
        return Conexion::execute(
            'INSERT INTO prestamo (fechaFinPrevista, observacion, estado, ci_solicitante, id_equipo) VALUES (?, ?, ?, ?, ?)',
            [
                $prestamo->getFechaFinPrevista(),
                $prestamo->getObservacion(),
                $prestamo->getEstado(),
                $prestamo->getCiSolicitante(),
                $prestamo->getIdEquipo(),
            ]
        );
    }

    /** Marca el préstamo como devuelto solo si seguía "Entregado". */
    public function marcarDevuelto(int $idPrestamo): bool
    {
        return Conexion::filasAfectadas(
            "UPDATE prestamo SET estado = 'Devuelto', fechaDevolucion = NOW() WHERE id_prestamo = ? AND estado = 'Entregado'",
            [$idPrestamo]
        ) === 1;
    }

    public function estaEntregado(int $idPrestamo): bool
    {
        return (int)Conexion::scalar(
            "SELECT COUNT(*) AS n FROM prestamo WHERE id_prestamo = ? AND estado = 'Entregado'",
            [$idPrestamo]
        ) > 0;
    }

    public function obtenerIdEquipo(int $idPrestamo): ?int
    {
        $valor = Conexion::scalar('SELECT id_equipo FROM prestamo WHERE id_prestamo = ?', [$idPrestamo]);
        return $valor === null ? null : (int)$valor;
    }

    /** Lista los préstamos; si se pasa una cédula, solo los de esa persona. */
    public function listarConDetalle(?int $ciSolicitante = null): array
    {
        $sql = 'SELECT p.*, u.nom, u.ape, e.modelo, e.marca, e.tipo
                FROM prestamo p
                JOIN usuario u ON p.ci_solicitante = u.ci_usuario
                JOIN equipo e ON p.id_equipo = e.id_equipo';

        if ($ciSolicitante !== null) {
            return Conexion::select($sql . ' WHERE p.ci_solicitante = ? ORDER BY p.id_prestamo DESC', [$ciSolicitante]);
        }

        return Conexion::select($sql . ' ORDER BY p.id_prestamo DESC');
    }

    public function contar(): int
    {
        return (int)Conexion::scalar('SELECT COUNT(*) AS n FROM prestamo');
    }

    public function contarPorEstado(string $estado): int
    {
        return (int)Conexion::scalar('SELECT COUNT(*) AS n FROM prestamo WHERE estado = ?', [$estado]);
    }
}
