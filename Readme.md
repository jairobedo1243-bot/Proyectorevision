# SGRSI – Sistema de Gestión de Recursos y Soporte Informático

Proyecto de Bachillerato Tecnológico – Primera entrega (Junio 2026)

## ¿Qué es?

SGRSI es un sistema web full-stack para gestionar los recursos informáticos y el soporte técnico de una institución educativa. Permite registrar equipos, usuarios, préstamos y tickets de soporte, con autenticación por roles y persistencia en base de datos.

## Requisitos

- **XAMPP** (Apache + PHP 8+ + MySQL)
- Navegador moderno (Chrome, Firefox, Edge)

## Instalación

1. Cloná o copiá el proyecto en `C:\xampp\htdocs\Proyectorevision-main\`
2. Iniciá Apache y MySQL desde el panel de XAMPP
3. Abrí `http://localhost/phpmyadmin`, creá la BD `BD_SGRSI` e importá `BD_SGRSI.sql`
4. Abrí `http://localhost/Proyectorevision-main/login.html` en el navegador

### Usuarios de prueba

| Rol | Email | Contraseña |
|-----|-------|------------|
| Administrador | juan.perez@utu.edu.uy | Perez123! |
| Soporte Técnico | ana.martinez@utu.edu.uy | Marti234! |
| Docente | maria.garcia@utu.edu.uy | Garcia456! |

## Módulos

| Página | Descripción | Backend |
|--------|-------------|---------|
| `login.html` | Inicio de sesión con autenticación | PHP/MySQL |
| `index.html` | Dashboard con verificación de sesión | PHP/MySQL |
| `indexRecursos.html` | Inventario de equipos (PC, Proyector, Television) | Cliente |
| `indexUsuarios.html` | CRUD de usuarios | PHP/MySQL |
| `indexPrestamos.html` | Registro y devolución de préstamos | Cliente |
| `indexTickets.html` | Mesa de ayuda: crear y cerrar tickets | Cliente |
| `indexReportes.html` | Resumen general del sistema | Cliente |
| `indexHistorial.html` | Historial de tickets y reportes | Cliente |

## Stack tecnológico

- **Frontend:** HTML5, CSS3 (variables, flexbox, grid, media queries), JavaScript vanilla (ES6+)
- **Backend:** PHP 8+ con MySQLi (consultas preparadas)
- **Base de datos:** MySQL (`BD_SGRSI` con 6 tablas: usuario, equipo, prestamo, ticket, servicio, historial)
- **Autenticación:** Sesiones PHP + bcrypt (`password_hash`/`password_verify`)
- **Estilos:** Bootstrap 5 (incluido localmente en `css/` y `js/`)

## Arquitectura

```
CLIENTE (fetch)  ──JSON──>  PHP API  ──SQL──>  MySQL
                        <──JSON──            <──resultados
```

- **Módulos con API:** Login + Usuarios (persisten en MySQL)
- **Módulos client-side:** Inventario, Préstamos, Tickets, Reportes, Historial (datos en memoria)

## Diseño

- **Color primario:** `#1e3a5f` (azul institucional oscuro)
- **Color secundario:** `#2563eb` (azul claro – navegación y botones)
- **Tipografía de títulos:** Segoe UI / system-ui
- **Tipografía de cuerpo:** Arial / Helvetica
- **Responsive:** Adaptable a mobile (breakpoint en 600px)

## Estructura del proyecto

```
ProyectoRevision/
├── login.html              # Login con autenticación
├── index.html              # Dashboard principal
├── indexRecursos.html      # Inventario de equipos
├── indexUsuarios.html      # Gestión de usuarios
├── indexPrestamos.html     # Préstamos de equipos
├── indexTickets.html       # Tickets de soporte
├── indexReportes.html      # Reportes del sistema
├── indexHistorial.html     # Historial
│
├── api_login.php           # API de autenticación (POST/GET)
├── api_usuarios.php        # API de CRUD usuarios (GET/POST/DELETE)
├── conexion_db.php         # Conexión a MySQL
│
├── styles.css              # Estilos globales
├── Recursos.js             # Lógica de inventario
├── usuarios.js             # Lógica de usuarios (fetch API)
├── prestamos.js            # Lógica de préstamos
├── tickets.js              # Lógica de tickets
├── reportes.js             # Lógica de reportes
├── historial.js            # Lógica de historial
│
├── BD_SGRSI.sql            # Esquema + datos de prueba
├── Readme.md               # Este archivo
├── GUIA_COMPLETA.md        # Guía de estudio detallada
│
├── css/                    # Bootstrap 5 CSS
├── js/                     # Bootstrap 5 JS
├── images/                 # Recursos gráficos
└── jairo/                  # Proyecto SQL Server (alternativo)
```

## Restricciones de negocio

- Tipos de equipo válidos: **PC**, **Proyector** y **Television**
- Un equipo no puede estar prestado a más de una persona al mismo tiempo
- No se pueden registrar dos usuarios con el mismo correo electrónico (UNIQUE en DB)
- Las contraseñas se almacenan con hash bcrypt (nunca en texto plano)

## Equipo de desarrollo

Completar con los nombres del equipo.
