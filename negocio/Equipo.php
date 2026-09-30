<?php

declare(strict_types=1);

class Equipo
{
    private string $numeroSerie;
    private string $modelo;
    private string $marca;
    private string $tipo;
    private string $estado;

    public function __construct(string $numeroSerie, string $modelo, string $marca, string $tipo)
    {
        $this->numeroSerie = $numeroSerie;
        $this->modelo = $modelo;
        $this->marca = $marca;
        $this->tipo = $tipo;
        $this->estado = 'Disponible';
    }

    public function getNumeroSerie(): string
    {
        return $this->numeroSerie;
    }

    public function getModelo(): string
    {
        return $this->modelo;
    }

    public function getMarca(): string
    {
        return $this->marca;
    }

    public function getTipo(): string
    {
        return $this->tipo;
    }

    public function getEstado(): string
    {
        return $this->estado;
    }
}
