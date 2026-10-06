<?php

declare(strict_types=1);
defined('ENTRYPOINT') || (http_response_code(404) && exit);
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
