-- -----------------------------------------------------------------------------
-- CARGA DE DATOS
-- -----------------------------------------------------------------------------

-- 1. TABLA: periods
INSERT INTO periods (id, name, start_time, end_time) VALUES
(1, '1° hora', '08:15:00', '09:10:00'),
(2, '2° hora', '09:10:00', '10:05:00'),
(3, '3° hora', '10:05:00', '11:00:00'),
(4, 'Recreo', '11:00:00', '11:30:00'),
(5, '4° hora', '11:30:00', '12:25:00'),
(6, '5° hora', '12:25:00', '13:20:00'),
(7, '6° hora', '13:20:00', '14:15:00');

-- 2. TABLA: classes (reemplaza a groups)
INSERT INTO classes (code, name, stage) VALUES
('2-DAW', '2º Des. Aplicaciones Web', 'CFGS'),
('1-SMR', '1º Sist. Micro. y Redes', 'CFGM'),
('2-SMR', '2º Sist. Micro. y Redes', 'CFGM'),
('1-GA', '1º Gestión Administrativa', 'CFGM'),
('1-BACH-A', '1º Bachillerato A', 'BACH'),
('1-BACH-B', '1º Bachillerato B', 'BACH');