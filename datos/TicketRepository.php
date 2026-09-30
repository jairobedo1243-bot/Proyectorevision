<?php

declare(strict_types=1);

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

    public function cerrar(int $idTicket): bool
    {
        return Conexion::execute(
            "UPDATE ticket SET estado = 'Cerrado', fechaFin = NOW() WHERE id_ticket = ?",
            [$idTicket]
        );
    }

    public function listarConSolicitante(): array
    {
        return Conexion::select(
            'SELECT t.*, u.nom, u.ape
             FROM ticket t
             JOIN usuario u ON t.ci_solicitante = u.ci_usuario
             ORDER BY t.id_ticket DESC'
        );
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
