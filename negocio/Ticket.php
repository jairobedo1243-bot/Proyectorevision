<?php

declare(strict_types=1);

class Ticket
{
    private string $titulo;
    private string $descripcion;
    private int $ciSolicitante;
    private string $prioridad;
    private string $estado;

    public function __construct(string $titulo, string $descripcion, int $ciSolicitante, string $prioridad)
    {
        $this->titulo = $titulo;
        $this->descripcion = $descripcion;
        $this->ciSolicitante = $ciSolicitante;
        $this->prioridad = $prioridad;
        $this->estado = 'Abierto';
    }

    public function getTitulo(): string
    {
        return $this->titulo;
    }

    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    public function getCiSolicitante(): int
    {
        return $this->ciSolicitante;
    }

    public function getPrioridad(): string
    {
        return $this->prioridad;
    }

    public function getEstado(): string
    {
        return $this->estado;
    }
}
