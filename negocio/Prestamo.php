<?php

declare(strict_types=1);
defined('ENTRYPOINT') || (http_response_code(404) && exit);
class Prestamo
{
    private int $ciSolicitante;
    private int $idEquipo;
    private string $fechaFinPrevista;
    private string $observacion;
    private string $estado;

    public function __construct(int $ciSolicitante, int $idEquipo, string $fechaFinPrevista, string $observacion)
    {
        $this->ciSolicitante = $ciSolicitante;
        $this->idEquipo = $idEquipo;
        $this->fechaFinPrevista = $fechaFinPrevista;
        $this->observacion = $observacion;
        $this->estado = 'Entregado';
    }

    public function getCiSolicitante(): int
    {
        return $this->ciSolicitante;
    }

    public function getIdEquipo(): int
    {
        return $this->idEquipo;
    }

    public function getFechaFinPrevista(): string
    {
        return $this->fechaFinPrevista;
    }

    public function getObservacion(): string
    {
        return $this->observacion;
    }

    public function getEstado(): string
    {
        return $this->estado;
    }
}
