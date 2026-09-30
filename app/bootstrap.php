<?php

declare(strict_types=1);

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Language.php';
require_once __DIR__ . '/SessionAuth.php';
require_once __DIR__ . '/App.php';

$idioma = Translator::currentLanguage();

$GLOBALS['idioma'] = $idioma;
