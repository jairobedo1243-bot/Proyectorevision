<?php

declare(strict_types=1);

class PageController
{
    public function __construct(private SessionAuth $auth)
    {
    }

    public function currentUser(): ?array
    {
        return $this->auth->getUser();
    }

    public function requireUser(): array
    {
        return $this->auth->requireLogin();
    }

    public function requirePageAccess(string $page): array
    {
        $user = $this->currentUser();
        return $this->auth->requirePageAccess($page, $user);
    }
}
