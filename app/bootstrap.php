<?php

declare(strict_types=1);
defined('ENTRYPOINT') || (http_response_code(404) && exit);

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Language.php';
require_once __DIR__ . '/SessionAuth.php';
require_once __DIR__ . '/App.php';

$idioma = Translator::currentLanguage();

$GLOBALS['idioma'] = $idioma;
