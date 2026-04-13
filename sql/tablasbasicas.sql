-- -----------------------------------------------------------------------------
-- CARGA DE DATOS
-- -----------------------------------------------------------------------------

-- 1. TABLA: periods
INSERT INTO periods (start_time, end_time) VALUES
('08:15:00', '09:10:00'),
('09:10:00', '10:05:00'),
('10:05:00', '11:00:00'),
('11:30:00', '12:25:00'),
('12:25:00', '13:20:00'),
('13:20:00', '14:15:00');

-- 2. TABLA: classes (reemplaza a groups)
INSERT INTO classes (code, name, stage) VALUES
('2-DAW', '2º Des. Aplicaciones Web', 'CFGS'),
('1-SMR', '1º Sist. Micro. y Redes', 'CFGM'),
('2-SMR', '2º Sist. Micro. y Redes', 'CFGM'),
('1-GA', '1º Gestión Administrativa', 'CFGM'),
('1-BACH-A', '1º Bachillerato A', 'BACH'),
('1-BACH-B', '1º Bachillerato B', 'BACH');