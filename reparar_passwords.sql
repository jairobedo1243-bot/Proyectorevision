-- ============================================================
-- reparar_passwords.sql
--
-- Regenera el hash de la contrasena de los 10 usuarios de prueba.
-- NO borra ni modifica ninguna otra tabla: solo actualiza la
-- columna contrasena de la tabla usuario.
--
-- Como usarlo:
--   phpMyAdmin -> elegir la base BD_SGRSI -> pestana Importar
--   -> elegir este archivo -> Continuar
-- ============================================================

USE BD_SGRSI;

-- juan.perez@utu.edu.uy  ->  Perez123!
UPDATE usuario SET contrasena = '$2y$12$8eJWS5l8pF2B0ie.i1isiOaLKG92kDQD58hJtU8.VtIRhnSR8myli' WHERE email = 'juan.perez@utu.edu.uy';

-- maria.garcia@utu.edu.uy  ->  Garcia456!
UPDATE usuario SET contrasena = '$2y$12$hCeE/V4ltTyXxLQ9yD1Lf.icZc/xc/rR0.mk3k4BPZdQbnXGm.tZy' WHERE email = 'maria.garcia@utu.edu.uy';

-- carlos.rodriguez@utu.edu.uy  ->  Rodri789!
UPDATE usuario SET contrasena = '$2y$12$4tG7mIl1uQynZvohj.w10.CXfLucBMSeTiB5pFoqlbdCVtqp.jQ8q' WHERE email = 'carlos.rodriguez@utu.edu.uy';

-- ana.martinez@utu.edu.uy  ->  Marti234!
UPDATE usuario SET contrasena = '$2y$12$H7PA8bS5pdhFckTjfAwE4uu0bmSP.Az0/WETVd32Ljd3z1SrNH5Z6' WHERE email = 'ana.martinez@utu.edu.uy';

-- lucia.fernandez@utu.edu.uy  ->  Ferna567!
UPDATE usuario SET contrasena = '$2y$12$N3SJTlNbhg1Da2w4w7gjhOFFdjyYkm/bkDUqblnTAHFFTz4ECXCnO' WHERE email = 'lucia.fernandez@utu.edu.uy';

-- pedro.silva@utu.edu.uy  ->  Silva890!
UPDATE usuario SET contrasena = '$2y$12$5onT29FB4pEEpGnjJTj2ye8.v9w3vJVWCPpibDnEOvMb5D0yFOaDi' WHERE email = 'pedro.silva@utu.edu.uy';

-- sofia.lopez@utu.edu.uy  ->  Lopez123!
UPDATE usuario SET contrasena = '$2y$12$JRlBBXEGkOyF8w.icesBSOxvDM4x1.rx6E4A/odk.avCCfdfa9ZX6' WHERE email = 'sofia.lopez@utu.edu.uy';

-- diego.torres@utu.edu.uy  ->  Torre456!
UPDATE usuario SET contrasena = '$2y$12$c1fzxVuMOlIeHhVrXbY0BuVPmBdE2/o6eFEHHHWZX3gbol.pMMNxy' WHERE email = 'diego.torres@utu.edu.uy';

-- martin.gonzalez@utu.edu.uy  ->  Gonza789!
UPDATE usuario SET contrasena = '$2y$12$wKoOIKUHzOh3tdRsSco3Hunkz5OwwULYNEim3h8VBrO.n/qDxRRsC' WHERE email = 'martin.gonzalez@utu.edu.uy';

-- valeria.castro@utu.edu.uy  ->  Castr012!
UPDATE usuario SET contrasena = '$2y$12$qAT.Sc0peI9X6H95MVK3xOPpe9gWQt7Iw.qQOsOqNpFfXoHvKFyMa' WHERE email = 'valeria.castro@utu.edu.uy';

