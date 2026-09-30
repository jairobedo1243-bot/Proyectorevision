drop DATABASE IF EXISTS BD_SGRSI;
CREATE DATABASE BD_SGRSI;

use BD_SGRSI;

-- ============================================================
-- Tabla usuario
--
-- ci_usuario es la CEDULA DE IDENTIDAD del usuario, sin puntos
-- ni guion (8 digitos). NO lleva auto_increment: la cedula la
-- ingresa quien da de alta al usuario, no la inventa MySQL.
-- ============================================================
create table usuario(
    ci_usuario int primary key,
    nom varchar(20) not null,
    ape varchar(20) not null,
    email varchar(80) not null unique,
    contrasena varchar(255) not null,
    rol varchar(20) not null
);

create table servicio(
    id_servicio int primary key auto_increment,
    fechaInServicio varchar(20) not null,
    descripcionServicio varchar(100) not null,
    fechaFinServicio varchar(20) not null,
    ci_usuario int not null,
    foreign key (ci_usuario) references usuario(ci_usuario)
);

create table equipo(
    id_equipo int primary key auto_increment,
    estado varchar(20) not null,
    numeroSerie varchar(20) not null,
    modelo varchar(20) not null,
    marca varchar(20) not null,
    tipo varchar(20) not null
);

create table prestamo(
    id_prestamo int primary key auto_increment,
    fechaCreacion varchar(20) not null,
    fechaFin varchar(20) not null,
    observacion varchar(50) not null,
    ci_usuario int not null,
    id_equipo int not null,
    foreign key (ci_usuario) references usuario(ci_usuario),
    foreign key (id_equipo)  references equipo(id_equipo)
);

create table ticket(
    id_ticket int primary key auto_increment,
    fechaCreacion varchar(20) not null,
    fechaFin varchar(20) not null,
    descripcion varchar(100) not null,
    ci_usuario int not null,
    prioridad varchar(20) not null,
    foreign key (ci_usuario) references usuario(ci_usuario)
);

create table historial(
    id_historial int primary key auto_increment,
    descripcion varchar(100) not null,
    accion varchar(100) not null,
    id_equipo int not null,
    id_ticket int not null,
    id_servicio int not null,
    foreign key (id_equipo)   references equipo(id_equipo),
    foreign key (id_ticket)   references ticket(id_ticket),
    foreign key (id_servicio) references servicio(id_servicio)
);


-- ============================================================
-- DATOS DE PRUEBA
-- ============================================================

-- Usuarios. La cedula es el numero de 8 digitos (7 + digito
-- verificador). Al final de cada linea, la contrasena en claro
-- para poder probar el login.
INSERT INTO usuario (ci_usuario, nom, ape, email, contrasena, rol) VALUES
(38147258, 'Juan', 'Perez', 'juan.perez@utu.edu.uy', '$2y$12$7/wb05YZpz4oqc4EowPO6.TmeK93jCb.GVmvu3QdCQwDoE/W57uU.', 'Administrador'),  -- Perez123!
(41920637, 'Maria', 'Garcia', 'maria.garcia@utu.edu.uy', '$2y$12$CJPwS5qEj.LqBQ5z5gagEOoT1WmEr4dIzIXPQQb.kzPYYRkFvdi.a', 'Docente'),  -- Garcia456!
(45308174, 'Carlos', 'Rodriguez', 'carlos.rodriguez@utu.edu.uy', '$2y$12$v4FEKAIaO0/o0cR1E2BLE.0d7w3GFlOVC1NWWGilHE/gdjM4haM5C', 'Docente'),  -- Rodri789!
(39672418, 'Ana', 'Martinez', 'ana.martinez@utu.edu.uy', '$2y$12$zS0p9b4p8Ie8jqXnn7ER8uGXOJqwBPtMwxsZUQET91pQeWA/9Y9gK', 'Tecnico'),  -- Marti234!
(50283947, 'Lucia', 'Fernandez', 'lucia.fernandez@utu.edu.uy', '$2y$12$pwIY/5oaQtxbbRuPAy7SW.ncEj8bUjDbZrTGDsXxTMIT0CxMVNxlC', 'Docente'),  -- Ferna567!
(46715083, 'Pedro', 'Silva', 'pedro.silva@utu.edu.uy', '$2y$12$CkzDKCpA61kVHdaVZsb0wuktlqSW4Y3I1uWYbq.9KgN.T0XEuKqwe', 'Docente'),  -- Silva890!
(51839262, 'Sofia', 'Lopez', 'sofia.lopez@utu.edu.uy', '$2y$12$xKTj15WCmdH8wNaKWpNoheSvUMEGmsTvNcB8GSsMjzB8mrWgsK0IG', 'Docente'),  -- Lopez123!
(37456105, 'Diego', 'Torres', 'diego.torres@utu.edu.uy', '$2y$12$63c3clrmQq6CIwBrgQHttOpiPxPTC18NUqB833GJX6M9YcFtF/Jc2', 'Administrador'),  -- Torre456!
(49082730, 'Martin', 'Gonzalez', 'martin.gonzalez@utu.edu.uy', '$2y$12$kSWYQCkji1Fpqdh36BISXOQu8b3pCm4PljPb8ozjSR4v5oVfLZYCu', 'Docente'),  -- Gonza789!
(52610499, 'Valeria', 'Castro', 'valeria.castro@utu.edu.uy', '$2y$12$Y9pQhyQBe6AOo.r/Iga1VOVO0zj6SjPam7UDXH6rjZKSQ53XdfHkG', 'Tecnico');  -- Castr012!

INSERT INTO equipo (id_equipo, estado, numeroSerie, modelo, marca, tipo) VALUES
(1, 'Disponible', 'SN-001-ABC', 'ProDesk 400', 'HP', 'Desktop'),
(2, 'Prestado', 'SN-002-DEF', 'ThinkPad X1', 'Lenovo', 'Laptop'),
(3, 'Disponible', 'SN-003-GHI', 'LS24R350', 'Samsung', 'Monitor'),
(4, 'En reparacion', 'SN-004-JKL', 'OptiPlex 3080', 'Dell', 'Desktop'),
(5, 'Disponible', 'SN-005-MNO', 'Pavilion 14', 'HP', 'Laptop'),
(6, 'Prestado', 'SN-006-PQR', 'PowerLite 1785W', 'Epson', 'Proyector'),
(7, 'Disponible', 'SN-007-STU', 'K120', 'Logitech', 'Teclado'),
(8, 'Disponible', 'SN-008-VWX', 'M720', 'Logitech', 'Mouse');

INSERT INTO servicio (id_servicio, fechaInServicio, descripcionServicio, fechaFinServicio, ci_usuario) VALUES
(1, '2026-02-10', 'Mantenimiento preventivo de equipos informaticos', '2026-02-14', 39672418),
(2, '2026-03-01', 'Actualizacion de software en laboratorio 3', '2026-03-05', 52610499),
(3, '2026-03-15', 'Configuracion de red en aula virtual', '2026-03-18', 39672418),
(4, '2026-04-01', 'Instalacion de proyectores en salones', '2026-04-08', 52610499),
(5, '2026-04-20', 'Respaldo de datos de administracion', '2026-04-22', 39672418);

INSERT INTO ticket (id_ticket, fechaCreacion, fechaFin, descripcion, ci_usuario, prioridad) VALUES
(1, '2026-02-12', '2026-02-13', 'Pantalla azul al iniciar sesion en equipo del laboratorio', 45308174, 'Alta'),
(2, '2026-03-02', '2026-03-03', 'No funciona el mouse del gabinete de informatica', 50283947, 'Media'),
(3, '2026-03-10', '2026-03-11', 'Proyector no enciende en el salon 203', 41920637, 'Alta'),
(4, '2026-03-20', '2026-03-22', 'No se conecta a la red WiFi institucional', 51839262, 'Media'),
(5, '2026-04-05', '2026-04-06', 'Impresora no responde en sala de docentes', 46715083, 'Baja'),
(6, '2026-04-15', '2026-04-16', 'Teclado con teclas trabadas en equipo del estudiante', 49082730, 'Media'),
(7, '2026-05-01', '2026-05-04', 'Fallo en el suministro electrico del laboratorio 1', 37456105, 'Alta');

INSERT INTO prestamo (id_prestamo, fechaCreacion, fechaFin, observacion, ci_usuario, id_equipo) VALUES
(1, '2026-02-20', '2026-02-27', 'Prestamo de laptop para trabajo en clase', 41920637, 2),
(2, '2026-03-05', '2026-03-12', 'Proyector para exposicion de proyectos', 46715083, 6),
(3, '2026-03-25', '2026-03-28', 'Equipo para taller de programacion', 45308174, 5),
(4, '2026-04-10', '2026-04-17', 'Monitor para practica de diseno grafico', 51839262, 3),
(5, '2026-05-05', '2026-05-12', 'Laptop para curso de capacitacion docente', 41920637, 2);

INSERT INTO historial (id_historial, descripcion, accion, id_equipo, id_ticket, id_servicio) VALUES
(1, 'Se reinstalo sistema operativo en equipo del laboratorio', 'Reparacion', 1, 1, 1),
(2, 'Se reemplazo mouse defectuoso en gabinete', 'Reemplazo', 8, 2, 2),
(3, 'Se reparo proyector del salon 203, lampara reemplazada', 'Reparacion', 6, 3, 4),
(4, 'Se configuro red WiFi en dispositivo del estudiante', 'Configuracion', 2, 4, 3),
(5, 'Se limpio y realizo mantenimiento preventivo a desktop', 'Mantenimiento', 4, 7, 5),
(6, 'Se reemplazo teclado con teclas trabadas', 'Reemplazo', 7, 6, 2);
