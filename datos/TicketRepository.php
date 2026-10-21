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

    /** Lista los tickets; si se pasa una cédula, solo los de esa persona. */
    public function listarConSolicitante(?int $ciSolicitante = null): array
    {
        $sql = 'SELECT t.*, u.nom, u.ape
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
