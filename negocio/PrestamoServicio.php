<?php

declare(strict_types=1);

class PrestamoServicio
{
    private PrestamoRepository $prestamos;
    private EquipoRepository $equipos;

    public function __construct(?PrestamoRepository $prestamos = null, ?EquipoRepository $equipos = null)
    {
        $this->prestamos = $prestamos ?? new PrestamoRepository();
        $this->equipos = $equipos ?? new EquipoRepository();
    }

    /** Registra el préstamo y deja el equipo como "Prestado". */
    public function registrar(Prestamo $prestamo): bool
    {
        // Solo se presta un equipo que realmente está disponible.
        if ($this->equipos->obtenerEstado($prestamo->getIdEquipo()) !== 'Disponible') {
            return false;
        }

        if (!$this->prestamos->guardar($prestamo)) {
            return false;
        }

        $this->equipos->cambiarEstado($prestamo->getIdEquipo(), 'Prestado');
        return true;
    }

    /** Marca el préstamo como devuelto y el equipo vuelve a "Disponible". */
    public function devolver(int $idPrestamo): bool
    {
        // Si el préstamo ya fue devuelto no se toca el equipo (podría estar prestado a otra persona).
        if (!$this->prestamos->estaEntregado($idPrestamo)) {
            return false;
        }

        $idEquipo = $this->prestamos->obtenerIdEquipo($idPrestamo);
        $this->prestamos->marcarDevuelto($idPrestamo);

        if ($idEquipo !== null) {
            $this->equipos->cambiarEstado($idEquipo, 'Disponible');
        }

        return true;
    }
}
