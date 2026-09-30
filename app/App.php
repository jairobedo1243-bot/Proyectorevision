<?php

declare(strict_types=1);

class App
{
    public function __construct(
        public SessionAuth $auth,
        public Translator $translator
    ) {
    }

    public static function bootstrap(): self
    {
        $translator = new Translator();
        $auth = new SessionAuth();
        return new self($auth, $translator);
    }
}
