# SGRSI – Sistema de Gestión de Recursos y Soporte Informático

Proyecto de Bachillerato Tecnológico – Primera entrega (Junio 2026)

## ¿Qué es?

SGRSI es un sistema web para gestionar los recursos informáticos y el soporte técnico de una institución educativa.
Permite registrar equipos, usuarios, préstamos y tickets de soporte, todo desde el navegador.

## Cómo abrirlo

1. Descomprimí la carpeta del proyecto.
2. Abrí el archivo `index.html` en cualquier navegador moderno (Chrome, Firefox, Edge).
3. Navegá usando el menú superior.

No requiere instalación ni servidor.

## Módulos

| Página               | Descripción                                              |
|----------------------|----------------------------------------------------------|
| `index.html`         | Inicio – resumen y acceso rápido a cada módulo           |
| `indexRecursos.html` | Inventario de equipos (PC, Proyector, Television)        |
| `indexUsuarios.html` | Alta de usuarios (Docente, Técnico, Administrador)       |
| `indexPrestamos.html`| Registro y devolución de préstamos de equipos            |
| `indexTickets.html`  | Mesa de ayuda: crear y cerrar tickets de soporte         |
| `indexReportes.html` | Resumen general del estado del sistema                   |
| `indexHistorial.html`| Historial de tickets y reportes anteriores               |

## Tecnologías usadas

- HTML5 semántico
- CSS3 con variables personalizadas y media queries (responsivo)
- JavaScript vanilla (sin librerías externas)
- Bootstrap 5 (incluido localmente, disponible para uso futuro)

## Diseño

- **Color primario:** `#1e3a5f` (azul institucional oscuro)
- **Color secundario:** `#2563eb` (azul claro – navegación y botones)
- **Tipografía de títulos:** Segoe UI / system-ui
- **Tipografía de cuerpo:** Arial / Helvetica

## Estructura del proyecto

```
ProyectoRevision/
├── index.html
├── indexRecursos.html
├── indexUsuarios.html
├── indexPrestamos.html
├── indexTickets.html
├── indexReportes.html
├── indexHistorial.html
├── styles.css
├── Recursos.js
├── usuarios.js
├── prestamos.js
├── tickets.js
├── reportes.js
├── historial.js
├── Readme.md
├── css/           ← Bootstrap CSS
├── js/            ← Bootstrap JS
└── images/
```

## Usuarios del sistema (según MER)

- **Docente** – solicita préstamos y reporta problemas
- **Técnico** – gestiona equipos y atiende tickets
- **Administrador** – acceso completo al sistema

## Restricciones de negocio implementadas

- Los tipos de equipo válidos son: **PC**, **Proyector** y **Television** (según el MER).
- Un equipo no puede estar prestado a más de una persona al mismo tiempo.
- No se pueden registrar dos usuarios con el mismo correo electrónico.

## Equipo de desarrollo

Completar con los nombres del equipo.
