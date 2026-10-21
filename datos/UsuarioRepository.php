<?php

declare(strict_types=1);
defined('ENTRYPOINT') || (http_response_code(404) && exit);

/**
 * Acceso a la tabla usuario.
 * La baja es LÓGICA (RF-06 / RN-10): un usuario dado de baja queda con activo = 0.
 * No puede iniciar sesión ni aparece en las listas, pero su registro se conserva,
 * así sus préstamos, tickets y servicios mantienen la integridad referencial.
 */
class UsuarioRepository
{
    /**
     * Da de alta al usuario. Si la cédula es de alguien que fue dado de baja, no se crea
     * otra fila (la cédula es la clave): se REACTIVA ese mismo registro con los datos y la
     * clave inicial nuevos, y conserva su historial de préstamos, tickets y servicios.
     * Devuelve false si la cédula ya está activa o el correo es de otra persona.
     */
    public function guardar(Usuario $usuario): bool
    {
        $porCi = Conexion::selectOne('SELECT ci_usuario, activo FROM usuario WHERE ci_usuario = ?', [$usuario->getCi()]);
        $porEmail = Conexion::selectOne('SELECT ci_usuario FROM usuario WHERE email = ?', [$usuario->getEmail()]);

        if ($porCi === null) {
            if ($porEmail !== null) {
                return false;
            }
            return Conexion::execute(
                'INSERT INTO usuario (ci_usuario, nom, ape, email, contrasena, rol, debe_cambiar_clave) VALUES (?, ?, ?, ?, ?, ?, 1)',
                [
                    $usuario->getCi(),
                    $usuario->getNom(),
                    $usuario->getApe(),
                    $usuario->getEmail(),
                    $usuario->getContrasenaHash(),
                    $usuario->getRol(),
                ]
            );
        }

        // La cédula ya existe: solo se puede volver a dar de alta si estaba dada de baja,
        // y el correo no puede ser de otra cédula.
        if ((int)$porCi['activo'] === 1 || ($porEmail !== null && (int)$porEmail['ci_usuario'] !== $usuario->getCi())) {
            return false;
        }

        return Conexion::filasAfectadas(
            'UPDATE usuario SET nom = ?, ape = ?, email = ?, contrasena = ?, rol = ?, activo = 1, debe_cambiar_clave = 1
             WHERE ci_usuario = ? AND activo = 0',
            [
                $usuario->getNom(),
                $usuario->getApe(),
                $usuario->getEmail(),
                $usuario->getContrasenaHash(),
                $usuario->getRol(),
                $usuario->getCi(),
            ]
        ) === 1;
    }

    /** true si la cédula pertenece a un usuario dado de baja (se va a reactivar al darlo de alta). */
    public function estaDadoDeBaja(int $ci): bool
    {
        return (int)Conexion::scalar(
            'SELECT COUNT(*) AS n FROM usuario WHERE ci_usuario = ? AND activo = 0',
            [$ci]
        ) > 0;
    }

    /**
     * Da de baja al usuario (baja lógica). No borra nada: sus préstamos, tickets
     * y servicios siguen en la base con su nombre.
     * Devuelve false si el usuario no existe o ya estaba dado de baja.
     */
    public function darDeBaja(int $ci): bool
    {
        return Conexion::filasAfectadas(
            'UPDATE usuario SET activo = 0 WHERE ci_usuario = ? AND activo = 1',
            [$ci]
        ) === 1;
    }

    /** true si la cédula pertenece a un usuario que no fue dado de baja. */
    public function estaActivo(int $ci): bool
    {
        return (int)Conexion::scalar(
            'SELECT COUNT(*) AS n FROM usuario WHERE ci_usuario = ? AND activo = 1',
            [$ci]
        ) > 0;
    }

    /** true si el usuario todavía tiene la clave inicial que le pasó el Administrador. */
    public function debeCambiarClave(int $ci): bool
    {
        return (int)Conexion::scalar(
            'SELECT COUNT(*) AS n FROM usuario WHERE ci_usuario = ? AND debe_cambiar_clave = 1',
            [$ci]
        ) > 0;
    }

    /** Devuelve el hash de la clave actual (para comprobar la clave inicial antes de cambiarla). */
    public function obtenerHashClave(int $ci): ?string
    {
        $hash = Conexion::scalar('SELECT contrasena FROM usuario WHERE ci_usuario = ? AND activo = 1', [$ci]);
        return $hash === null ? null : (string)$hash;
    }

    /** Guarda la clave nueva y deja de exigir el cambio. */
    public function cambiarClave(int $ci, string $hashNuevo): bool
    {
        return Conexion::filasAfectadas(
            'UPDATE usuario SET contrasena = ?, debe_cambiar_clave = 0 WHERE ci_usuario = ? AND activo = 1',
            [$hashNuevo, $ci]
        ) === 1;
    }

    /** Para el login: solo se buscan usuarios activos (RF-01). */
    public function buscarPorEmail(string $email): ?array
    {
        return Conexion::selectOne('SELECT * FROM usuario WHERE email = ? AND activo = 1', [$email]);
    }

    /** Listado de usuarios activos (RF-05). */
    public function listar(): array
    {
        return Conexion::select('SELECT ci_usuario, nom, ape, email, rol FROM usuario WHERE activo = 1 ORDER BY ci_usuario DESC');
    }

    public function listarSolicitantes(): array
    {
        return Conexion::select("SELECT ci_usuario, nom, ape FROM usuario WHERE rol = 'Solicitante' AND activo = 1 ORDER BY nom");
    }

    public function listarSolicitantesYTecnicos(): array
    {
        return Conexion::select("SELECT ci_usuario, nom, ape FROM usuario WHERE rol IN ('Solicitante', 'Tecnico') AND activo = 1 ORDER BY nom");
    }

    /** Usuarios activos, para elegir a quién se le abre un ticket. */
    public function listarParaSeleccion(): array
    {
        return Conexion::select('SELECT ci_usuario, nom, ape FROM usuario WHERE activo = 1 ORDER BY nom, ape');
    }
}
