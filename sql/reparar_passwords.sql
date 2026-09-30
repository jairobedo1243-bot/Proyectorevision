-- ============================================================
-- reparar_passwords.sql
-- Regenera el hash de la contrasena de los 10 usuarios de prueba.
-- Solo actualiza la columna contrasena de la tabla usuario.
-- Uso: phpMyAdmin -> base BD_SGRSI -> Importar -> este archivo.
-- ============================================================
USE BD_SGRSI;

UPDATE usuario SET contrasena = '$2y$12$1c627uRL/EkCrJQVXMNof.KhuU5eLxIRWkjle7YWKvjO45AXPSI3m' WHERE email = 'juan.perez@utu.edu.uy';
UPDATE usuario SET contrasena = '$2y$12$mOobRFE.DyAThyc4q8ceqeHbLQUo.Teycd01ggs7koUjq5oHvrFgG' WHERE email = 'maria.garcia@utu.edu.uy';
UPDATE usuario SET contrasena = '$2y$12$UGiHHOGD5rS82D9ttPb2duVW.6lcJzoaF/QQTCvKmOJflFHWwzCcC' WHERE email = 'carlos.rodriguez@utu.edu.uy';
UPDATE usuario SET contrasena = '$2y$12$w4xZNlslv.tDhuomvX9SUOZK8yeYAhCKIFDAk37sjozwx2vodTcrq' WHERE email = 'ana.martinez@utu.edu.uy';
UPDATE usuario SET contrasena = '$2y$12$Z1rmHvUoGUp1YBHoe/UjXObMGgbv0l3DmFXq6f/u48JzPNTxk46Sa' WHERE email = 'lucia.fernandez@utu.edu.uy';
UPDATE usuario SET contrasena = '$2y$12$KeHepYIaE2sK60U9xKRgKu11sMWdo6bCgd2FN./fTpfPyrKWQzw2S' WHERE email = 'pedro.silva@utu.edu.uy';
UPDATE usuario SET contrasena = '$2y$12$5OypuUcBFxoDkmgm1I2jPenNmuddVwvD3RZMRE/UFMNHHEPUPsxGO' WHERE email = 'sofia.lopez@utu.edu.uy';
UPDATE usuario SET contrasena = '$2y$12$Cg1amuTm6HPuSm3uWp4ozO7.Ao.z48vq4PcApHQ2b2q.h5HWFuQyq' WHERE email = 'diego.torres@utu.edu.uy';
UPDATE usuario SET contrasena = '$2y$12$H6FY1olj3nb2JxNAVQOXr.2NKNTC9p6r0tg9o4zprUe3HRt//dfmO' WHERE email = 'martin.gonzalez@utu.edu.uy';
UPDATE usuario SET contrasena = '$2y$12$xkTT4M8saJGlcOHYI1e0MOdOJ9tohN6i1vDITtzSJ.5G.rnvilJI6' WHERE email = 'valeria.castro@utu.edu.uy';
