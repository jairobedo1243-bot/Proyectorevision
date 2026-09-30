# SGRSI – Sistema de Gestión de Recursos y Soporte Informático

Proyecto de Bachillerato Tecnológico – Primera entrega (Junio 2026)

## ¿Qué es?

SGRSI es un sistema web para gestionar los recursos informáticos y el soporte técnico de una institución educativa. Permite registrar equipos, usuarios, préstamos y tickets de soporte, con autenticación por sesión, permisos según el rol del usuario e interfaz en español e inglés.

Es una aplicación PHP renderizada en el servidor: cada página `.php` procesa la petición, consulta MySQL y devuelve el HTML ya armado. No hay una API separada ni JavaScript que pida datos al servidor.

## Requisitos

- **XAMPP** con Apache, PHP 8.0 o superior y MySQL/MariaDB
- Navegador moderno (Chrome, Firefox, Edge)

## Instalación

<<<<<<< HEAD
1. Copiá la carpeta del proyecto dentro de `htdocs` de XAMPP (por ejemplo `C:\xampp\htdocs\mi-proyecto\`).
2. Iniciá **Apache** y **MySQL** desde el panel de control de XAMPP.
3. Abrí `http://localhost/phpmyadmin`, entrá a la pestaña **Importar** y elegí `BD_SGRSI.sql`. El script borra y vuelve a crear la base `BD_SGRSI`, sus tablas y los datos de prueba, así que no hace falta crearla antes.
4. Creá un usuario de MySQL propio para la aplicación (NO uses `root`). En phpMyAdmin, pestaña **SQL**, ejecutá (cambiá la clave por una tuya, larga):
   ```sql
   CREATE USER 'sgrsi_app'@'localhost' IDENTIFIED BY 'TU_CLAVE_LARGA';
   GRANT SELECT, INSERT, UPDATE, DELETE ON BD_SGRSI.* TO 'sgrsi_app'@'localhost';
   ```
   Luego copiá `config/secreto.ejemplo.php` como `config/secreto.php` y poné ahí el mismo usuario y clave. Ese archivo no se sube a GitHub.
   Además, en `C:\xampp\mysql\bin\my.ini` agregá `bind-address=127.0.0.1` bajo `[mysqld]` y reiniciá MySQL, para que solo se pueda entrar desde esta computadora.
5. Abrí `http://localhost/<nombre-de-la-carpeta>/public/login.php` en el navegador.
=======
1. Copiá la carpeta del proyecto dentro de `htdocs` de XAMPP (por ejemplo `C:\xampp\htdocs\proyectorevision\`).
2. Iniciá **Apache** y **MySQL** desde el panel de control de XAMPP.
3. Abrí `http://localhost/phpmyadmin`, entrá a la pestaña **Importar** y elegí `BD_SGRSI.sql`. El script borra y vuelve a crear la base `BD_SGRSI`, sus tablas y los datos de prueba, así que no hace falta crearla antes.
4. Revisá la conexión en `app/Database.php` (host `localhost`, usuario `root`, clave `1234`) y ajustá la clave si tu MySQL usa otra.
5. Abrí `http://localhost/<nombre-de-la-carpeta>/login.php` en el navegador.
>>>>>>> cd9f9a295c82d8e371ccd90713e4be150cbbab5b

### Usuarios de prueba

| Rol | Email | Contraseña |
|-----|-------|------------|
| Administrador | juan.perez@utu.edu.uy | Perez123! |
| Técnico | ana.martinez@utu.edu.uy | Marti234! |
<<<<<<< HEAD
| Solicitante | maria.garcia@utu.edu.uy | Garcia456! |

Los usuarios creados desde la pantalla de usuarios reciben una clave inicial aleatoria que se muestra una sola vez en pantalla.

Si el login rechaza un usuario de prueba, importá `sql/reparar_passwords.sql` desde phpMyAdmin: regenera los hashes de los 10 usuarios de prueba.
=======
| Docente (perfil Solicitante) | maria.garcia@utu.edu.uy | Garcia456! |

Los usuarios creados desde la pantalla de usuarios reciben la clave inicial `Default123!`.

Si el login rechaza un usuario de prueba, importá `reparar_passwords.sql` desde phpMyAdmin: regenera los hashes de los 10 usuarios de prueba.
>>>>>>> cd9f9a295c82d8e371ccd90713e4be150cbbab5b

## Módulos

| Página | Descripción | Tablas |
|--------|-------------|--------|
| `login.php` | Inicio de sesión (correo y contraseña) | `usuario` |
| `logout.php` | Cierra la sesión y vuelve al login | — |
| `index.php` | Inicio con acceso a los módulos según el rol | — |
| `recursos.php` | Inventario: lista y alta de equipos | `equipo` |
| `usuarios.php` | Alta y baja de usuarios, con validación de la cédula | `usuario` |
| `prestamos.php` | Registro y devolución de préstamos | `prestamo`, `equipo`, `usuario` |
| `tickets.php` | Mesa de ayuda: abrir y cerrar tickets | `ticket`, `usuario` |
| `reportes.php` | Contadores de equipos, tickets y préstamos por estado | `equipo`, `ticket`, `prestamo` |
| `historial.php` | Historial que une equipo, ticket y servicio | `historial`, `equipo`, `ticket`, `servicio` |

## Roles y permisos

`SessionAuth::normalizeRole()` convierte el rol guardado en la base a uno de tres perfiles. Cualquier rol que no sea administrador ni técnico (por ejemplo `Docente`) se trata como Solicitante.

| Perfil | Páginas | Edita inventario | Administra usuarios | Cierra tickets |
|--------|---------|------------------|---------------------|----------------|
| Administrador | Todas | Sí | Sí | Sí |
| Técnico | Todas menos usuarios | No | No | Sí |
| Solicitante | Inicio, préstamos y tickets | No | No | No |

## Stack tecnológico

<<<<<<< HEAD
- **Lenguaje:** PHP 8+ (tipado estricto, clases en `negocio/` y `datos/`)
- **Base de datos:** MySQL con MySQLi y consultas preparadas
- **Autenticación:** sesiones PHP y contraseñas con bcrypt (`password_hash` / `password_verify`)
- **Idioma:** español e inglés (clase `Translator` en `public/includes/Language.php`, funciones `t()` y `tv()`)
- **Estilos:** `public/CSS/styles.css` y `public/CSS/login.css`

## Arquitectura en tres capas

```
Navegador ──> public/*.php ──> negocio/ ──> datos/ ──> MySQL (BD_SGRSI)
              (presentación)   (reglas)     (SQL)
```

Cada capa solo le habla a la siguiente: las páginas de `public/` usan clases de `negocio/` y de `datos/`; `negocio/` usa `datos/`; `datos/` es la única que escribe SQL. Ni `negocio/` ni `datos/` conocen HTML, `$_GET` ni `$_POST` (salvo la sesión en `SessionAuth`).

| Capa | Carpeta | Qué hay |
|------|---------|---------|
| Presentación | `public/` | Páginas PHP (HTML + lectura de `$_POST`/`$_GET`), `css/`, `js/`, `images/`, y `includes/` con el traductor (`t()`, `tv()`) y `bootstrap.php` |
| Negocio | `negocio/` | `Usuario` (validación de cédula), `Equipo`, `Prestamo`, `Ticket`, `PrestamoServicio` (prestar/devolver cambia el estado del equipo), `SessionAuth` (login, roles y permisos) |
| Datos | `datos/` | `Conexion` (MySQLi) y un repositorio por tabla: `UsuarioRepository`, `EquipoRepository`, `PrestamoRepository`, `TicketRepository`, `HistorialRepository` |
| Config | `config/` | `config.php`: credenciales de MySQL y autoload de las clases |

Las páginas siguen el mismo patrón: cargan `includes/bootstrap.php`, exigen sesión y permiso (`SessionAuth`), procesan el formulario creando un objeto de negocio y guardándolo con su repositorio, y dibujan el HTML.
=======
- **Lenguaje:** PHP 8+ (tipado estricto, clases en `app/`)
- **Base de datos:** MySQL con MySQLi y consultas preparadas
- **Autenticación:** sesiones PHP y contraseñas con bcrypt (`password_hash` / `password_verify`)
- **Idioma:** español e inglés (clase `Translator`, funciones `t()` y `tv()`)
- **Estilos:** `styles.css` y `login.css`, propios del proyecto

## Arquitectura

```
Navegador ──GET/POST──> página PHP ──require_once──> app/bootstrap.php
                            │                              │
                            │                     Database · SessionAuth · Translator
                            │                              │
                            └────────── SQL preparado ─────┴──> MySQL (BD_SGRSI)
```

Todas las páginas protegidas siguen el mismo patrón:

1. Cargan `app/bootstrap.php`.
2. `$auth->requireLogin()` exige sesión activa (si no, redirige a `login.php`).
3. `$auth->requirePageAccess('pagina.php', $usuario)` exige que el rol pueda ver esa página.
4. Procesan las acciones (`POST` para altas; `?eliminar=`, `?devolver=` y `?cerrar=` para cambios).
5. Leen los datos con `Database::select(...)` y dibujan el HTML, escribiendo los textos con `t('clave')`.

### Clases de `app/`

| Archivo | Responsabilidad |
|---------|-----------------|
| `bootstrap.php` | Carga las clases y detecta el idioma |
| `Database.php` | Conexión MySQLi única y métodos `select`, `selectOne`, `scalar`, `execute` |
| `SessionAuth.php` | Sesión, login/logout, roles y permisos por página |
| `Language.php` | `Translator` con los textos es/en, y las funciones `t()` y `tv()` |
| `App.php` | Agrupa `SessionAuth` y `Translator` |
| `PageController.php` | Envoltorio de `SessionAuth` para las páginas |

## Idioma

Agregar `?idioma=es` o `?idioma=en` a cualquier URL cambia el idioma y lo guarda en una cookie por un año. `t('clave')` traduce textos de la interfaz y `tv('Valor')` traduce valores que vienen de la base (roles, estados, tipos).
>>>>>>> cd9f9a295c82d8e371ccd90713e4be150cbbab5b

## Estructura del proyecto

```
<<<<<<< HEAD
mi-proyecto/
├── public/
│   ├── index.php, login.php, logout.php
│   ├── recursos.php, usuarios.php, prestamos.php
│   ├── tickets.php, reportes.php, historial.php
│   ├── generar_hash.php        # herramienta de desarrollo (borrar al entregar)
│   ├── includes/               # bootstrap.php, Language.php (t() y tv())
│   ├── css/                    # styles.css, login.css
│   ├── js/                     # Bootstrap 5 local (las páginas actuales no lo enlazan)
│   └── images/
├── negocio/
│   ├── Usuario.php, Equipo.php, Prestamo.php, Ticket.php
│   ├── PrestamoServicio.php
│   └── SessionAuth.php
├── datos/
│   ├── Conexion.php
│   └── UsuarioRepository.php, EquipoRepository.php, PrestamoRepository.php,
│       TicketRepository.php, HistorialRepository.php
├── config/
│   └── config.php
├── sql/                        # BD_SGRSI.sql, reparar_passwords.sql
└── jairo/                      # Proyecto SQL Server alternativo
=======
proyectorevision/
├── app/
│   ├── bootstrap.php           # Carga las clases y el idioma
│   ├── App.php
│   ├── Database.php            # Conexión y consultas MySQL
│   ├── Language.php            # Translator, t() y tv()
│   ├── PageController.php
│   └── SessionAuth.php         # Sesión, roles y permisos
│
├── login.php                   # Inicio de sesión
├── logout.php                  # Cierre de sesión
├── index.php                   # Inicio
├── recursos.php                # Inventario de equipos
├── usuarios.php                # Gestión de usuarios
├── prestamos.php               # Préstamos de equipos
├── tickets.php                 # Tickets de soporte
├── reportes.php                # Reportes del sistema
├── historial.php               # Historial
├── db.php                      # Conexión global (compatibilidad)
├── lang.php                    # Solo carga bootstrap (compatibilidad)
│
├── styles.css                  # Estilos globales
├── login.css                   # Estilos del login
├── images/                     # Recursos gráficos
├── css/, js/                   # Bootstrap 5 local (las páginas actuales no lo enlazan)
│
├── BD_SGRSI.sql                # Esquema y datos de prueba
├── reparar_passwords.sql       # Regenera los hashes de los usuarios de prueba
├── generar_hash.php            # Herramienta de desarrollo (borrar al entregar)
├── Readme.md                   # Este archivo
├── jairo/                      # Proyecto SQL Server alternativo
└── proyectorevision/           # Copia anterior del proyecto (no se usa)
>>>>>>> cd9f9a295c82d8e371ccd90713e4be150cbbab5b
```

## Base de datos

`BD_SGRSI` tiene seis tablas:

| Tabla | Clave primaria | Claves foráneas |
|-------|----------------|-----------------|
| `usuario` | `ci_usuario` (cédula, sin auto_increment) | — |
| `equipo` | `id_equipo` | — |
| `prestamo` | `id_prestamo` | `ci_usuario`, `id_equipo` |
| `ticket` | `id_ticket` | `ci_usuario` |
| `servicio` | `id_servicio` | `ci_usuario` |
| `historial` | `id_historial` | `id_equipo`, `id_ticket`, `id_servicio` |

## Restricciones de negocio

- La cédula se valida con su dígito verificador (8 dígitos) antes de dar de alta un usuario.
- No se pueden registrar dos usuarios con el mismo correo (`UNIQUE` en la base).
- Las contraseñas se guardan con hash bcrypt, nunca en texto plano.
- El formulario de préstamos solo ofrece equipos con estado `Disponible`; al prestar, el equipo pasa a `Prestado`, y al devolverlo vuelve a `Disponible`.
- Solo Administrador y Técnico pueden cerrar tickets; solo Administrador gestiona usuarios.

## Pendientes conocidos

<<<<<<< HEAD
- `public/prestamos.php`, `public/tickets.php` y `public/reportes.php` consultan columnas que `BD_SGRSI.sql` no crea (`estado`, `ci_solicitante`, `fechaFinPrevista`, `fechaDevolucion` en `prestamo`; `titulo`, `estado`, `ci_solicitante` en `ticket`). Hay que alinear el esquema SQL con el código, o el código con el esquema.
- El SQL de prueba usa el rol `Docente`, pero `prestamos.php` filtra solicitantes por `rol = "Solicitante"`.
- Los datos se imprimen sin `htmlspecialchars`; conviene escaparlos para evitar XSS.
- Borrar, devolver y cerrar se hacen con enlaces `GET`; conviene pasarlos a `POST`.
- Las credenciales de MySQL están escritas en `config/config.php`.
- Borrar `public/generar_hash.php` y los usuarios de prueba del login antes de publicar.
=======
- `prestamos.php`, `tickets.php` y `reportes.php` consultan columnas que `BD_SGRSI.sql` no crea (`estado`, `ci_solicitante`, `fechaFinPrevista`, `fechaDevolucion` en `prestamo`; `titulo`, `estado`, `ci_solicitante` en `ticket`). Hay que alinear el esquema SQL con el código, o el código con el esquema.
- El SQL de prueba usa el rol `Docente`, pero `prestamos.php` filtra solicitantes por `rol = "Solicitante"`.
- Los datos se imprimen sin `htmlspecialchars`; conviene escaparlos para evitar XSS.
- Borrar, devolver y cerrar se hacen con enlaces `GET`; conviene pasarlos a `POST`.
- Las credenciales de MySQL están escritas en `app/Database.php`.
- Borrar `generar_hash.php` y los usuarios de prueba del login antes de publicar.
>>>>>>> cd9f9a295c82d8e371ccd90713e4be150cbbab5b

## Equipo de desarrollo

Completar con los nombres del equipo.
