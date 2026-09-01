<?php

declare(strict_types=1);

class SessionAuth
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
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
                'etiqueta' => tv('Administrador'),
                'clase' => 'badge-danger',
                'paginas' => ['index.php', 'recursos.php', 'usuarios.php', 'prestamos.php', 'tickets.php', 'reportes.php', 'historial.php'],
                'inventarioEditar' => true,
                'usuariosAdministrar' => true,
                'ticketsCerrar' => true,
            ],
            'tecnico' => [
                'etiqueta' => tv('Tecnico'),
                'clase' => 'badge-warning',
                'paginas' => ['index.php', 'recursos.php', 'prestamos.php', 'tickets.php', 'reportes.php', 'historial.php'],
                'inventarioEditar' => false,
                'usuariosAdministrar' => false,
                'ticketsCerrar' => true,
            ],
            'solicitante' => [
                'etiqueta' => tv('Solicitante'),
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
