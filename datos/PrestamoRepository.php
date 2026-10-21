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

    /** Devuelve el préstamo solo si pertenece a esa persona (para precargar el formulario de edición). */
    public function obtenerPropio(int $idPrestamo, int $ciSolicitante): ?array
    {
        return Conexion::selectOne(
            'SELECT * FROM prestamo WHERE id_prestamo = ? AND ci_solicitante = ?',
            [$idPrestamo, $ciSolicitante]
        );
    }

    /**
     * Edita la fecha de devolución prevista y la observación de un préstamo propio.
     * Solo se puede mientras siga "Entregado". El equipo no se cambia: si hay que
     * cambiarlo, se devuelve y se pide otro préstamo.
     */
    public function actualizarPropio(int $idPrestamo, int $ciSolicitante, string $fechaFinPrevista, string $observacion): bool
    {
        $prestamo = $this->obtenerPropio($idPrestamo, $ciSolicitante);
        if ($prestamo === null || $prestamo['estado'] !== 'Entregado') {
            return false;
        }

        return Conexion::execute(
            'UPDATE prestamo SET fechaFinPrevista = ?, observacion = ? WHERE id_prestamo = ? AND ci_solicitante = ?',
            [$fechaFinPrevista, $observacion, $idPrestamo, $ciSolicitante]
        );
    }

    /**
     * Lista los préstamos; si se pasa una cédula, solo los de esa persona.
     * Incluye u.activo para marcar a los solicitantes dados de baja (sus préstamos se conservan).
     */
    public function listarConDetalle(?int $ciSolicitante = null): array
    {
        $sql = 'SELECT p.*, u.nom, u.ape, u.activo, e.modelo, e.marca, e.tipo
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
