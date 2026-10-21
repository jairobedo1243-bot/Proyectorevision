<?php
// Plantilla: copiá este archivo como config/secreto.php y poné tus datos.
// secreto.php NO se sube a GitHub (está en .gitignore).
defined('ENTRYPOINT') || (http_response_code(404) && exit);
define('DB_USER', 'sgrsi_app');
define('DB_PASS', 'TU_CLAVE_LARGA');
// Descomentá si tu MySQL/MariaDB no usa el puerto 3306:
// define('DB_PORT', 3310);
