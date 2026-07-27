drop DATABASE IF EXISTS BD_SGRSI;
CREATE DATABASE BD_SGRSI;

use BD_SGRSI;

create table usuario(
    ci_usuario int primary key auto_increment,
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
    foreign key (id_equipo) references equipo(id_equipo)
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
    foreign key (id_equipo) references equipo(id_equipo),
    foreign key (id_ticket) references ticket(id_ticket),
    foreign key (id_servicio) references servicio(id_servicio)
);
USE BD_SGRSI;

INSERT INTO usuario (ci_usuario, nom, ape, email, contrasena, rol) VALUES
(1, 'Juan', 'Perez', 'juan.perez@utu.edu.uy', '$2b$10$pEeC/1uA7YVP3pi8pcEdiOqds8q8XN8K.P.A6Ol5NSvFygV78mpzK', 'Administrador'),      -- Perez123!
(2, 'Maria', 'Garcia', 'maria.garcia@utu.edu.uy', '$2b$10$k2arqsHI2myjUddxcsab6OhETlRjSU3Jd7Z2wkM.OMNouxFSSLngy', 'Docente'),        -- Garcia456!
(3, 'Carlos', 'Rodriguez', 'carlos.rodriguez@utu.edu.uy', '$2b$10$itFrDOigcgBMNOpZP0R1RuP4RqKkXY6lsC7i.Mfrd1vLNl6BZuHHm', 'Estudiante'), -- Rodri789!
(4, 'Ana', 'Martinez', 'ana.martinez@utu.edu.uy', '$2b$10$CzPchQ3eGe0FPpJ3Lk4SRu1HFz0SrVyTBD1ue94WKYzfWIHTm1y/m', 'Soporte Tecnico'), -- Marti234!
(5, 'Lucia', 'Fernandez', 'lucia.fernandez@utu.edu.uy', '$2b$10$BajVz2aV3cIdqJMublLvsefk5cydZlQjdR2uHNJJ1gu70PKaf/WGS', 'Estudiante'), -- Ferna567!
(6, 'Pedro', 'Silva', 'pedro.silva@utu.edu.uy', '$2b$10$BnzhtzsG9bwKyHVVlNkyLO7CMLS18etudq3YZJdBeESRXigaAPvpm', 'Docente'),          -- Silva890!
(7, 'Sofia', 'Lopez', 'sofia.lopez@utu.edu.uy', '$2b$10$38YiceGUGaev93vhYs552OF2G9VFw4dme2D3/gHlsAW47uAFT5kF2', 'Estudiante'),      -- Lopez123!
(8, 'Diego', 'Torres', 'diego.torres@utu.edu.uy', '$2b$10$jr4Mwt77d7X292caaKwtSe8WDNJvb0XCuCuX0ZeN/0DJknWkWzLbi', 'Coordinador'),   -- Torre456!
(9, 'Martin', 'Gonzalez', 'martin.gonzalez@utu.edu.uy', '$2b$10$ObWduzi65Mwh9tq2A796SOrvxlwNVW.9DDPncQry400TN4yroLG2a', 'Estudiante'), -- Gonza789!
(10, 'Valeria', 'Castro', 'valeria.castro@utu.edu.uy', '$2b$10$OGSKrYvxxP9387bQpkr78.ePk320Ofgcah0Rra5Az5zZRjmbfHIhm', 'Soporte Tecnico'); -- Castr012!

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
(1, '2026-02-10', 'Mantenimiento preventivo de equipos informaticos', '2026-02-14', 4),
(2, '2026-03-01', 'Actualizacion de software en laboratorio 3', '2026-03-05', 10),
(3, '2026-03-15', 'Configuracion de red en aula virtual', '2026-03-18', 4),
(4, '2026-04-01', 'Instalacion de proyectores en salones', '2026-04-08', 10),
(5, '2026-04-20', 'Respaldo de datos de administracion', '2026-04-22', 4);

INSERT INTO ticket (id_ticket, fechaCreacion, fechaFin, descripcion, ci_usuario, prioridad) VALUES
(1, '2026-02-12', '2026-02-13', 'Pantalla azul al iniciar sesion en equipo del laboratorio', 3, 'Alta'),
(2, '2026-03-02', '2026-03-03', 'No funciona el mouse del gabinete de informatica', 5, 'Media'),
(3, '2026-03-10', '2026-03-11', 'Proyector no enciende en el salon 203', 2, 'Alta'),
(4, '2026-03-20', '2026-03-22', 'No se conecta a la red WiFi institucional', 7, 'Media'),
(5, '2026-04-05', '2026-04-06', 'Impresora no responde en sala de docentes', 6, 'Baja'),
(6, '2026-04-15', '2026-04-16', 'Teclado con teclas trabadas en equipo del estudiante', 9, 'Media'),
(7, '2026-05-01', '2026-05-04', 'Fallo en el suministro electrico del laboratorio 1', 8, 'Alta');

INSERT INTO prestamo (id_prestamo, fechaCreacion, fechaFin, observacion, ci_usuario, id_equipo) VALUES
(1, '2026-02-20', '2026-02-27', 'Prestamo de laptop para trabajo en clase', 2, 2),
(2, '2026-03-05', '2026-03-12', 'Proyector para exposicion de proyectos', 6, 6),
(3, '2026-03-25', '2026-03-28', 'Equipo para taller de programacion', 3, 5),
(4, '2026-04-10', '2026-04-17', 'Monitor para practica de diseno grafico', 7, 3),
(5, '2026-05-05', '2026-05-12', 'Laptop para curso de capacitacion docente', 2, 2);

INSERT INTO historial (id_historial, descripcion, accion, id_equipo, id_ticket, id_servicio) VALUES
(1, 'Se reinstalo sistema operativo en equipo del laboratorio', 'Reparacion', 1, 1, 1),
(2, 'Se reemplazo mouse defectuoso en gabinete', 'Reemplazo', 8, 2, 2),
(3, 'Se reparo proyector del salon 203, lampara reemplazada', 'Reparacion', 6, 3, 4),
(4, 'Se configuro red WiFi en dispositivo del estudiante', 'Configuracion', 2, 4, 3),
(5, 'Se limpio y realizo mantenimiento preventivo a desktop', 'Mantenimiento', 4, 7, 5),
(6, 'Se reemplazo teclado con teclas trabadas', 'Reemplazo', 7, 6, 2);

-- el pepe 123
-- el pepe 1234
