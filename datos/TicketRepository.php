<?php

declare(strict_types=1);
defined('ENTRYPOINT') || (http_response_code(404) && exit);
class TicketRepository
{
    public function guardar(Ticket $ticket): bool
    {
        return Conexion::execute(
            'INSERT INTO ticket (titulo, descripcion, ci_solicitante, prioridad, estado) VALUES (?, ?, ?, ?, ?)',
            [
                $ticket->getTitulo(),
                $ticket->getDescripcion(),
                $ticket->getCiSolicitante(),
                $ticket->getPrioridad(),
                $ticket->getEstado(),
            ]
        );
    }

    /** Cierra el ticket. Devuelve false si no existía o ya estaba cerrado. */
    public function cerrar(int $idTicket): bool
    {
        return Conexion::filasAfectadas(
            "UPDATE ticket SET estado = 'Cerrado', fechaFin = NOW() WHERE id_ticket = ? AND estado != 'Cerrado'",
            [$idTicket]
        ) === 1;
    }

    /** Devuelve el ticket solo si pertenece a esa persona (para precargar el formulario de edición). */
    public function obtenerPropio(int $idTicket, int $ciSolicitante): ?array
    {
        return Conexion::selectOne(
            'SELECT * FROM ticket WHERE id_ticket = ? AND ci_solicitante = ?',
            [$idTicket, $ciSolicitante]
        );
    }

    /**
     * Edita un ticket propio. Solo se puede mientras no esté cerrado.
     * Devuelve false si no es de esa persona, está cerrado o la base lo rechaza.
     * (No usa filas afectadas porque MySQL devuelve 0 si no cambió ningún valor.)
     */
    public function actualizarPropio(int $idTicket, int $ciSolicitante, string $titulo, string $descripcion, string $prioridad): bool
    {
        $ticket = $this->obtenerPropio($idTicket, $ciSolicitante);
        if ($ticket === null || $ticket['estado'] === 'Cerrado') {
            return false;
        }

        return Conexion::execute(
            'UPDATE ticket SET titulo = ?, descripcion = ?, prioridad = ? WHERE id_ticket = ? AND ci_solicitante = ?',
            [$titulo, $descripcion, $prioridad, $idTicket, $ciSolicitante]
        );
    }

    /**
     * Lista los tickets; si se pasa una cédula, solo los de esa persona.
     * Incluye u.activo para marcar a los solicitantes dados de baja (sus tickets se conservan).
     */
    public function listarConSolicitante(?int $ciSolicitante = null): array
    {
        $sql = 'SELECT t.*, u.nom, u.ape, u.activo
                FROM ticket t
                JOIN usuario u ON t.ci_solicitante = u.ci_usuario';

        if ($ciSolicitante !== null) {
            return Conexion::select($sql . ' WHERE t.ci_solicitante = ? ORDER BY t.id_ticket DESC', [$ciSolicitante]);
        }

        return Conexion::select($sql . ' ORDER BY t.id_ticket DESC');
    }

    public function contarAbiertos(): int
    {
        return (int)Conexion::scalar("SELECT COUNT(*) AS n FROM ticket WHERE estado != 'Cerrado'");
    }

    public function contarCerrados(): int
    {
        return (int)Conexion::scalar("SELECT COUNT(*) AS n FROM ticket WHERE estado = 'Cerrado'");
    }
}
