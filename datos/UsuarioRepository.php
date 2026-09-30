<?php

declare(strict_types=1);

class UsuarioRepository
{
    public function guardar(Usuario $usuario): bool
    {
        return Conexion::execute(
            'INSERT INTO usuario (ci_usuario, nom, ape, email, contrasena, rol) VALUES (?, ?, ?, ?, ?, ?)',
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

    public function eliminar(int $ci): bool
    {
        return Conexion::execute('DELETE FROM usuario WHERE ci_usuario = ?', [$ci]);
    }

    public function buscarPorEmail(string $email): ?array
    {
        return Conexion::selectOne('SELECT * FROM usuario WHERE email = ?', [$email]);
    }

    public function listar(): array
    {
        return Conexion::select('SELECT ci_usuario, nom, ape, email, rol FROM usuario ORDER BY ci_usuario DESC');
    }

    public function listarSolicitantes(): array
    {
        return Conexion::select("SELECT ci_usuario, nom, ape FROM usuario WHERE rol = 'Solicitante' ORDER BY nom");
    }

    public function listarSolicitantesYTecnicos(): array
    {
        return Conexion::select("SELECT ci_usuario, nom, ape FROM usuario WHERE rol IN ('Solicitante', 'Tecnico') ORDER BY nom");
    }
}
