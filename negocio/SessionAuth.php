<?php

declare(strict_types=1);
defined('ENTRYPOINT') || (http_response_code(404) && exit);
class SessionAuth
{
    private UsuarioRepository $usuarios;

    public function __construct(?UsuarioRepository $usuarios = null)
    {
        $this->usuarios = $usuarios ?? new UsuarioRepository();

        $this->iniciarSesion();
    }

    private function iniciarSesion(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Cookie de sesión más segura: JS no la puede leer y no viaja en otros sitios.
            ini_set('session.use_strict_mode', '1');
            session_set_cookie_params([
                'path' => '/',
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            session_start();
        }
    }

    public function start(): void
    {
        $this->iniciarSesion();
    }

    public function isLoggedIn(): bool
    {
        return isset($_SESSION['usuario']);
    }

    public function getUser(): ?array
    {
        return $_SESSION['usuario'] ?? null;
    }

    public function login(array $user): void
    {
        $_SESSION['usuario'] = $user;
    }

    /**
     * Valida correo y contraseña. Si son correctos abre la sesión y devuelve true.
     */
    public function autenticar(string $email, string $contrasena): bool
    {
        // Si hubo muchos intentos fallidos, se bloquea 1 minuto.
        if ($this->estaBloqueado()) {
            return false;
        }

        $usuario = $this->usuarios->buscarPorEmail($email);

        if ($usuario === null || !password_verify($contrasena, (string)($usuario['contrasena'] ?? ''))) {
            $_SESSION['intentos'] = (int)($_SESSION['intentos'] ?? 0) + 1;
            if ($_SESSION['intentos'] >= 5) {
                $_SESSION['bloqueo_hasta'] = time() + 60;
                $_SESSION['intentos'] = 0;
            }
            usleep(400000); // frena los ataques de fuerza bruta
            return false;
        }

        // Sesión nueva al entrar (evita el robo de sesión)
        session_regenerate_id(true);
        $_SESSION['intentos'] = 0;

        $this->login([
            'ci_usuario' => $usuario['ci_usuario'],
            'nom' => $usuario['nom'],
            'ape' => $usuario['ape'],
            'email' => $usuario['email'],
            'rol' => $usuario['rol'],
        ]);

        return true;
    }

    public function estaBloqueado(): bool
    {
        return (int)($_SESSION['bloqueo_hasta'] ?? 0) > time();
    }

    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }

    public function requireLogin(): array
    {
        if (!$this->isLoggedIn()) {
            header('Location: login.php');
            exit;
        }

        return $this->getUser();
    }

    public function permissionsForRole(string $role): array
    {
        $role = $this->normalizeRole($role);

        $permissions = [
            'administrador' => [
                'etiqueta' => 'Administrador',
                'clase' => 'badge-danger',
                'paginas' => ['index.php', 'recursos.php', 'usuarios.php', 'prestamos.php', 'tickets.php', 'reportes.php', 'historial.php'],
                'inventarioEditar' => true,
                'usuariosAdministrar' => true,
                'ticketsCerrar' => true,
            ],
            'tecnico' => [
                'etiqueta' => 'Tecnico',
                'clase' => 'badge-warning',
                'paginas' => ['index.php', 'recursos.php', 'prestamos.php', 'tickets.php', 'reportes.php', 'historial.php'],
                'inventarioEditar' => false,
                'usuariosAdministrar' => false,
                'ticketsCerrar' => true,
            ],
            'solicitante' => [
                'etiqueta' => 'Solicitante',
                'clase' => 'badge-info',
                'paginas' => ['index.php', 'prestamos.php', 'tickets.php'],
                'inventarioEditar' => false,
                'usuariosAdministrar' => false,
                'ticketsCerrar' => false,
            ],
        ];

        return $permissions[$role] ?? $permissions['solicitante'];
    }

    public function normalizeRole(string $role): string
    {
        $normalized = mb_strtolower(trim($role));

        if (str_contains($normalized, 'admin')) {
            return 'administrador';
        }

        if (str_contains($normalized, 'tecnico') || str_contains($normalized, 'técnico')) {
            return 'tecnico';
        }

        return 'solicitante';
    }

    public function requirePageAccess(string $page, ?array $user = null): array
    {
        $targetUser = $user ?? $this->requireLogin();
        $permiso = $this->permissionsForRole($targetUser['rol'] ?? 'Solicitante');

        if (!in_array($page, $permiso['paginas'], true)) {
            header('Location: index.php');
            exit;
        }

        return $permiso;
    }
}
