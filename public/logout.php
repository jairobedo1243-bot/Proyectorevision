<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$auth = new SessionAuth();
$auth->logout();
header('Location: login.php');
exit;
