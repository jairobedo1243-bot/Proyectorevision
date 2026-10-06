<?php

declare(strict_types=1);
defined('ENTRYPOINT') || (http_response_code(404) && exit);
class HistorialRepository
{
    public function listarDetallado(): array
    {
        return Conexion::select(
            'SELECT h.*, e.modelo, e.marca,
                    t.descripcion AS descripcionTicket,
                    s.descripcionServicio AS descripcionServicio
             FROM historial h
             JOIN equipo e ON h.id_equipo = e.id_equipo
             LEFT JOIN ticket t ON h.id_ticket = t.id_ticket
             LEFT JOIN servicio s ON h.id_servicio = s.id_servicio
             ORDER BY h.id_historial DESC'
        );
    }
}
