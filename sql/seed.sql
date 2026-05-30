-- =============================================================
-- SEED DATA – substitute-management
-- Ejecutar después de instalacion.sql (solo schema)
-- =============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE substitutions;
TRUNCATE TABLE absence_period;
TRUNCATE TABLE absences;
TRUNCATE TABLE event_schedules;
TRUNCATE TABLE event_teachers;
TRUNCATE TABLE events;
TRUNCATE TABLE schedules;
TRUNCATE TABLE teachers;
TRUNCATE TABLE classes;
TRUNCATE TABLE periods;
SET FOREIGN_KEY_CHECKS = 1;

-- =============================================================
-- PERÍODOS
-- =============================================================
INSERT INTO periods (id, name, start_time, end_time) VALUES
(1, '1 hora', '08:15:00', '09:10:00'),
(2, '2 hora', '09:10:00', '10:05:00'),
(3, '3 hora', '10:05:00', '11:00:00'),
(4, 'Recreo', '11:00:00', '11:30:00'),
(5, '4 hora', '11:30:00', '12:25:00'),
(6, '5 hora', '12:25:00', '13:20:00'),
(7, '6 hora', '13:20:00', '14:15:00');

-- =============================================================
-- CLASES
-- =============================================================
INSERT INTO classes (id, code, name, stage) VALUES
(1, '2-DAW',    '2º Des. Aplicaciones Web',  'CFGS'),
(2, '1-SMR',    '1º Sist. Micro. y Redes',   'CFGM'),
(3, '2-SMR',    '2º Sist. Micro. y Redes',   'CFGM'),
(4, '1-GA',     '1º Gestión Administrativa',  'CFGM'),
(5, '1-BACH-A', '1º Bachillerato A',          'BACH'),
(6, '1-BACH-B', '1º Bachillerato B',          'BACH');

-- =============================================================
-- PROFESORES  (password = "password" en bcrypt)
-- =============================================================
INSERT INTO teachers (id, full_name, email, phone, password_hash, substitution_counter, is_tutor, enabled) VALUES
(1, 'Ana García López',      'ana.garcia@centro.es',      '611111001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 0, 1),
(2, 'Carlos Martínez Ruiz',  'carlos.martinez@centro.es', '611111002', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 0, 1),
(3, 'María Sánchez Torres',  'maria.sanchez@centro.es',   '611111003', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 0, 1),
(4, 'Juan López Fernández',  'juan.lopez@centro.es',      '611111004', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 0, 1),
(5, 'Laura Gómez Díaz',      'laura.gomez@centro.es',     '611111005', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 0, 1),
(6, 'Pedro Ramírez Castro',  'pedro.ramirez@centro.es',   '611111006', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 1, 1),
(7, 'Isabel Moreno Vega',    'isabel.moreno@centro.es',   '611111007', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, 1, 1),
(8, 'Antonio Jiménez Ruiz',  'antonio.jimenez@centro.es', '611111008', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 5, 0, 1);

-- =============================================================
-- HORARIOS  (240 filas: 8 profesores × 5 días × 6 períodos)
--
-- Patrón por día (períodos lectivos 1,2,3,5,6,7 – sin recreo):
--   L: P1=C1  P2=C1  P3=C2  P5=C1  P6=G   P7=C2
--   M: P1=C2  P2=G   P3=C1  P5=C2  P6=C1  P7=C2
--   X: P1=C1  P2=C2  P3=G   P5=C1  P6=C2  P7=C1
--   J: P1=C2  P2=C1  P3=C2  P5=G   P6=C1  P7=C2
--   V: P1=C1  P2=C2  P3=C1  P5=C2  P6=G   P7=C1
-- Clases por profesor:
--   T1 Ana:      C1=1(2-DAW)    C2=2(1-SMR)
--   T2 Carlos:   C1=2(1-SMR)    C2=3(2-SMR)
--   T3 María:    C1=4(1-GA)     C2=5(1-BACH-A)
--   T4 Juan:     C1=5(1-BACH-A) C2=6(1-BACH-B)
--   T5 Laura:    C1=3(2-SMR)    C2=4(1-GA)
--   T6 Pedro:    C1=6(1-BACH-B) C2=1(2-DAW)
--   T7 Isabel:   C1=1(2-DAW)    C2=2(1-SMR)
--   T8 Antonio:  C1=NULL        C2=NULL  (siempre guardia)
-- =============================================================
INSERT INTO schedules (teacher_id, class_id, period_id, day) VALUES
-- T1 Ana
(1,1,1,'L'),(1,1,2,'L'),(1,2,3,'L'),(1,1,5,'L'),(1,NULL,6,'L'),(1,2,7,'L'),
(1,2,1,'M'),(1,NULL,2,'M'),(1,1,3,'M'),(1,2,5,'M'),(1,1,6,'M'),(1,2,7,'M'),
(1,1,1,'X'),(1,2,2,'X'),(1,NULL,3,'X'),(1,1,5,'X'),(1,2,6,'X'),(1,1,7,'X'),
(1,2,1,'J'),(1,1,2,'J'),(1,2,3,'J'),(1,NULL,5,'J'),(1,1,6,'J'),(1,2,7,'J'),
(1,1,1,'V'),(1,2,2,'V'),(1,1,3,'V'),(1,2,5,'V'),(1,NULL,6,'V'),(1,1,7,'V'),
-- T2 Carlos
(2,2,1,'L'),(2,2,2,'L'),(2,3,3,'L'),(2,2,5,'L'),(2,NULL,6,'L'),(2,3,7,'L'),
(2,3,1,'M'),(2,NULL,2,'M'),(2,2,3,'M'),(2,3,5,'M'),(2,2,6,'M'),(2,3,7,'M'),
(2,2,1,'X'),(2,3,2,'X'),(2,NULL,3,'X'),(2,2,5,'X'),(2,3,6,'X'),(2,2,7,'X'),
(2,3,1,'J'),(2,2,2,'J'),(2,3,3,'J'),(2,NULL,5,'J'),(2,2,6,'J'),(2,3,7,'J'),
(2,2,1,'V'),(2,3,2,'V'),(2,2,3,'V'),(2,3,5,'V'),(2,NULL,6,'V'),(2,2,7,'V'),
-- T3 María
(3,4,1,'L'),(3,4,2,'L'),(3,5,3,'L'),(3,4,5,'L'),(3,NULL,6,'L'),(3,5,7,'L'),
(3,5,1,'M'),(3,NULL,2,'M'),(3,4,3,'M'),(3,5,5,'M'),(3,4,6,'M'),(3,5,7,'M'),
(3,4,1,'X'),(3,5,2,'X'),(3,NULL,3,'X'),(3,4,5,'X'),(3,5,6,'X'),(3,4,7,'X'),
(3,5,1,'J'),(3,4,2,'J'),(3,5,3,'J'),(3,NULL,5,'J'),(3,4,6,'J'),(3,5,7,'J'),
(3,4,1,'V'),(3,5,2,'V'),(3,4,3,'V'),(3,5,5,'V'),(3,NULL,6,'V'),(3,4,7,'V'),
-- T4 Juan
(4,5,1,'L'),(4,5,2,'L'),(4,6,3,'L'),(4,5,5,'L'),(4,NULL,6,'L'),(4,6,7,'L'),
(4,6,1,'M'),(4,NULL,2,'M'),(4,5,3,'M'),(4,6,5,'M'),(4,5,6,'M'),(4,6,7,'M'),
(4,5,1,'X'),(4,6,2,'X'),(4,NULL,3,'X'),(4,5,5,'X'),(4,6,6,'X'),(4,5,7,'X'),
(4,6,1,'J'),(4,5,2,'J'),(4,6,3,'J'),(4,NULL,5,'J'),(4,5,6,'J'),(4,6,7,'J'),
(4,5,1,'V'),(4,6,2,'V'),(4,5,3,'V'),(4,6,5,'V'),(4,NULL,6,'V'),(4,5,7,'V'),
-- T5 Laura
(5,3,1,'L'),(5,3,2,'L'),(5,4,3,'L'),(5,3,5,'L'),(5,NULL,6,'L'),(5,4,7,'L'),
(5,4,1,'M'),(5,NULL,2,'M'),(5,3,3,'M'),(5,4,5,'M'),(5,3,6,'M'),(5,4,7,'M'),
(5,3,1,'X'),(5,4,2,'X'),(5,NULL,3,'X'),(5,3,5,'X'),(5,4,6,'X'),(5,3,7,'X'),
(5,4,1,'J'),(5,3,2,'J'),(5,4,3,'J'),(5,NULL,5,'J'),(5,3,6,'J'),(5,4,7,'J'),
(5,3,1,'V'),(5,4,2,'V'),(5,3,3,'V'),(5,4,5,'V'),(5,NULL,6,'V'),(5,3,7,'V'),
-- T6 Pedro
(6,6,1,'L'),(6,6,2,'L'),(6,1,3,'L'),(6,6,5,'L'),(6,NULL,6,'L'),(6,1,7,'L'),
(6,1,1,'M'),(6,NULL,2,'M'),(6,6,3,'M'),(6,1,5,'M'),(6,6,6,'M'),(6,1,7,'M'),
(6,6,1,'X'),(6,1,2,'X'),(6,NULL,3,'X'),(6,6,5,'X'),(6,1,6,'X'),(6,6,7,'X'),
(6,1,1,'J'),(6,6,2,'J'),(6,1,3,'J'),(6,NULL,5,'J'),(6,6,6,'J'),(6,1,7,'J'),
(6,6,1,'V'),(6,1,2,'V'),(6,6,3,'V'),(6,1,5,'V'),(6,NULL,6,'V'),(6,6,7,'V'),
-- T7 Isabel
(7,1,1,'L'),(7,1,2,'L'),(7,2,3,'L'),(7,1,5,'L'),(7,NULL,6,'L'),(7,2,7,'L'),
(7,2,1,'M'),(7,NULL,2,'M'),(7,1,3,'M'),(7,2,5,'M'),(7,1,6,'M'),(7,2,7,'M'),
(7,1,1,'X'),(7,2,2,'X'),(7,NULL,3,'X'),(7,1,5,'X'),(7,2,6,'X'),(7,1,7,'X'),
(7,2,1,'J'),(7,1,2,'J'),(7,2,3,'J'),(7,NULL,5,'J'),(7,1,6,'J'),(7,2,7,'J'),
(7,1,1,'V'),(7,2,2,'V'),(7,1,3,'V'),(7,2,5,'V'),(7,NULL,6,'V'),(7,1,7,'V'),
-- T8 Antonio (siempre guardia)
(8,NULL,1,'L'),(8,NULL,2,'L'),(8,NULL,3,'L'),(8,NULL,5,'L'),(8,NULL,6,'L'),(8,NULL,7,'L'),
(8,NULL,1,'M'),(8,NULL,2,'M'),(8,NULL,3,'M'),(8,NULL,5,'M'),(8,NULL,6,'M'),(8,NULL,7,'M'),
(8,NULL,1,'X'),(8,NULL,2,'X'),(8,NULL,3,'X'),(8,NULL,5,'X'),(8,NULL,6,'X'),(8,NULL,7,'X'),
(8,NULL,1,'J'),(8,NULL,2,'J'),(8,NULL,3,'J'),(8,NULL,5,'J'),(8,NULL,6,'J'),(8,NULL,7,'J'),
(8,NULL,1,'V'),(8,NULL,2,'V'),(8,NULL,3,'V'),(8,NULL,5,'V'),(8,NULL,6,'V'),(8,NULL,7,'V');

-- =============================================================
-- AUSENCIAS  (7 ausencias históricas + actuales + futuras)
-- id  teacher  fecha        tipo          justificada
--  1  Carlos   2026-05-20   Médica        Sí
--  2  María    2026-05-21   Personal      No
--  3  Ana      2026-05-25   Formación     No
--  4  Juan     2026-05-26   Médica        Sí
--  5  Laura    2026-05-27   Personal      No
--  6  Pedro    2026-05-28   Médica        No  ← HOY (guardias PENDIENTES)
--  7  Isabel   2026-06-02   Personal      No  ← PRÓXIMA
-- =============================================================
INSERT INTO absences (id, teacher_id, reason, date, proof_file_path, is_justified, viewed, created_at) VALUES
(1, 2, 'Médica - Cita con especialista digestivo',         '2026-05-20', '/uploads/absences/just_carlos.pdf', 1, 1, '2026-05-19 14:32:00'),
(2, 3, 'Personal - Gestión de documentación oficial',      '2026-05-21', NULL,                                0, 1, '2026-05-20 19:15:00'),
(3, 1, 'Formación - Jornada de formación permanente',      '2026-05-25', NULL,                                0, 1, '2026-05-24 18:00:00'),
(4, 4, 'Médica - Cirugía ambulatoria de rodilla',          '2026-05-26', '/uploads/absences/just_juan.pdf',   1, 1, '2026-05-25 09:10:00'),
(5, 5, 'Personal - Avería en transporte, tren cancelado',  '2026-05-27', NULL,                                0, 0, '2026-05-27 07:45:00'),
(6, 6, 'Médica - Revisión urgente con cardiólogo',         '2026-05-28', NULL,                                0, 0, '2026-05-27 21:03:00'),
(7, 7, 'Personal - Asuntos familiares urgentes',           '2026-06-02', NULL,                                0, 0, '2026-05-31 17:20:00');

-- =============================================================
-- PERÍODOS DE AUSENCIA
-- Cada ausencia tiene 2 períodos afectados con instrucciones
-- =============================================================
INSERT INTO absence_period (absence_id, period_id, instruction_material_url, comments, is_cover_generated) VALUES
-- Ausencia 1 (Carlos, miércoles 20-may, períodos 1 y 2)
(1, 1, NULL, 'Continuar ejercicios del tema 5 de redes. Libro página 78.', TRUE),
(1, 2, NULL, 'Dejar repasar apuntes del tema 4. Pueden trabajar en grupo.', TRUE),
-- Ausencia 2 (María, jueves 21-may, períodos 1 y 3)
(2, 1, NULL, 'Ficha de actividades en mi carpeta azul del cajón. Ejercicios 1 al 8.', TRUE),
(2, 3, NULL, 'Lectura libre del libro de texto o estudio personal.', TRUE),
-- Ausencia 3 (Ana, lunes 25-may, períodos 2 y 3)
(3, 2, NULL, 'Práctica HTML/CSS: maquetación de portfolio. Carpeta compartida Drive.', TRUE),
(3, 3, NULL, 'Repaso de subredes y máscaras. Calculadora online permitida.', TRUE),
-- Ausencia 4 (Juan, martes 26-may, períodos 1 y 5)
(4, 1, NULL, 'Examen parcial en el cajón de mi mesa. 50 minutos. Luego corrección.', TRUE),
(4, 5, NULL, 'Si acaban el examen, corrección en la pizarra entre todos.', TRUE),
-- Ausencia 5 (Laura, miércoles 27-may, períodos 1 y 2)
(5, 1, NULL, 'Repaso general: base de datos relacionales. Tema 6 del libro.', FALSE),
(5, 2, NULL, 'Ejercicios de normalización 1FN-3FN. Apuntes en la plataforma Moodle.', FALSE),
-- Ausencia 6 (Pedro, jueves 28-may HOY, períodos 1 y 2)
(6, 1, NULL, 'Práctica Java: funciones recursivas. NetBeans disponible en equipos.', FALSE),
(6, 2, NULL, 'Si terminan la práctica, documentar con Javadoc. Subir a Campus Virtual.', FALSE),
-- Ausencia 7 (Isabel, lunes 2-jun, períodos 2 y 3)
(7, 2, NULL, 'Continuar proyecto final DAW. Repositorio GitHub del grupo.', FALSE),
(7, 3, NULL, 'Revisar documentación y añadir diagrama de casos de uso.', FALSE);

-- =============================================================
-- SUSTITUCIONES
-- Usamos SELECT subqueries para obtener schedule_id sin hardcodear.
-- Sustitutos asignados (historial confirmado):
--   AP1 (Carlos P1 X) → Antonio (T8)  CONFIRMADO
--   AP2 (Carlos P2 X) → Ana    (T1)   CONFIRMADO  ← Ana ve esta guardia
--   AP3 (María  P1 J) → Antonio (T8)  CONFIRMADO
--   AP4 (María  P3 J) → sin asignar   CANCELADO
--   AP5 (Ana    P2 L) → Carlos  (T2)  CONFIRMADO
--   AP6 (Ana    P3 L) → Antonio (T8)  CONFIRMADO
--   AP7 (Juan   P1 M) → María   (T3)  CONFIRMADO
--   AP8 (Juan   P5 M) → Pedro   (T6)  CONFIRMADO
--   AP9 (Laura  P1 X) → Ana     (T1)  CONFIRMADO  ← Ana ve esta guardia
--  AP10 (Laura  P2 X) → Antonio (T8)  CONFIRMADO
--  AP11 (Pedro  P1 J) → PENDIENTE     ← HOY
--  AP12 (Pedro  P2 J) → PENDIENTE     ← HOY
--  AP13 (Isabel P2 L) → PENDIENTE     ← FUTURA
--  AP14 (Isabel P3 L) → PENDIENTE     ← FUTURA
-- =============================================================

-- AP1: Carlos ausente X período 1 → Antonio sustituye
INSERT INTO substitutions (absence_detail_id, schedule_id, absent_teacher_id, class_id, date, substitute_teacher_id, status, is_notified, enabled)
SELECT 1, s.id, 2, s.class_id, '2026-05-20', 8, 'CONFIRMADO', 1, 1
FROM schedules s WHERE s.teacher_id=2 AND s.period_id=1 AND s.day='X' AND s.class_id IS NOT NULL LIMIT 1;

-- AP2: Carlos ausente X período 2 → Ana sustituye
INSERT INTO substitutions (absence_detail_id, schedule_id, absent_teacher_id, class_id, date, substitute_teacher_id, status, is_notified, enabled)
SELECT 2, s.id, 2, s.class_id, '2026-05-20', 1, 'CONFIRMADO', 1, 1
FROM schedules s WHERE s.teacher_id=2 AND s.period_id=2 AND s.day='X' AND s.class_id IS NOT NULL LIMIT 1;

-- AP3: María ausente J período 1 → Antonio sustituye
INSERT INTO substitutions (absence_detail_id, schedule_id, absent_teacher_id, class_id, date, substitute_teacher_id, status, is_notified, enabled)
SELECT 3, s.id, 3, s.class_id, '2026-05-21', 8, 'CONFIRMADO', 1, 1
FROM schedules s WHERE s.teacher_id=3 AND s.period_id=1 AND s.day='J' AND s.class_id IS NOT NULL LIMIT 1;

-- AP4: María ausente J período 3 → CANCELADO (clase cancelada)
INSERT INTO substitutions (absence_detail_id, schedule_id, absent_teacher_id, class_id, date, substitute_teacher_id, status, is_notified, enabled)
SELECT 4, s.id, 3, s.class_id, '2026-05-21', NULL, 'CANCELADO', 0, 1
FROM schedules s WHERE s.teacher_id=3 AND s.period_id=3 AND s.day='J' AND s.class_id IS NOT NULL LIMIT 1;

-- AP5: Ana ausente L período 2 → Carlos sustituye
INSERT INTO substitutions (absence_detail_id, schedule_id, absent_teacher_id, class_id, date, substitute_teacher_id, status, is_notified, enabled)
SELECT 5, s.id, 1, s.class_id, '2026-05-25', 2, 'CONFIRMADO', 1, 1
FROM schedules s WHERE s.teacher_id=1 AND s.period_id=2 AND s.day='L' AND s.class_id IS NOT NULL LIMIT 1;

-- AP6: Ana ausente L período 3 → Antonio sustituye
INSERT INTO substitutions (absence_detail_id, schedule_id, absent_teacher_id, class_id, date, substitute_teacher_id, status, is_notified, enabled)
SELECT 6, s.id, 1, s.class_id, '2026-05-25', 8, 'CONFIRMADO', 1, 1
FROM schedules s WHERE s.teacher_id=1 AND s.period_id=3 AND s.day='L' AND s.class_id IS NOT NULL LIMIT 1;

-- AP7: Juan ausente M período 1 → María sustituye
INSERT INTO substitutions (absence_detail_id, schedule_id, absent_teacher_id, class_id, date, substitute_teacher_id, status, is_notified, enabled)
SELECT 7, s.id, 4, s.class_id, '2026-05-26', 3, 'CONFIRMADO', 1, 1
FROM schedules s WHERE s.teacher_id=4 AND s.period_id=1 AND s.day='M' AND s.class_id IS NOT NULL LIMIT 1;

-- AP8: Juan ausente M período 5 → Pedro sustituye
INSERT INTO substitutions (absence_detail_id, schedule_id, absent_teacher_id, class_id, date, substitute_teacher_id, status, is_notified, enabled)
SELECT 8, s.id, 4, s.class_id, '2026-05-26', 6, 'CONFIRMADO', 1, 1
FROM schedules s WHERE s.teacher_id=4 AND s.period_id=5 AND s.day='M' AND s.class_id IS NOT NULL LIMIT 1;

-- AP9: Laura ausente X período 1 → Ana sustituye
INSERT INTO substitutions (absence_detail_id, schedule_id, absent_teacher_id, class_id, date, substitute_teacher_id, status, is_notified, enabled)
SELECT 9, s.id, 5, s.class_id, '2026-05-27', 1, 'CONFIRMADO', 1, 1
FROM schedules s WHERE s.teacher_id=5 AND s.period_id=1 AND s.day='X' AND s.class_id IS NOT NULL LIMIT 1;

-- AP10: Laura ausente X período 2 → Antonio sustituye
INSERT INTO substitutions (absence_detail_id, schedule_id, absent_teacher_id, class_id, date, substitute_teacher_id, status, is_notified, enabled)
SELECT 10, s.id, 5, s.class_id, '2026-05-27', 8, 'CONFIRMADO', 1, 1
FROM schedules s WHERE s.teacher_id=5 AND s.period_id=2 AND s.day='X' AND s.class_id IS NOT NULL LIMIT 1;

-- AP11: Pedro ausente J período 1 → PENDIENTE (HOY)
INSERT INTO substitutions (absence_detail_id, schedule_id, absent_teacher_id, class_id, date, status, enabled)
SELECT 11, s.id, 6, s.class_id, '2026-05-28', 'PENDIENTE', 1
FROM schedules s WHERE s.teacher_id=6 AND s.period_id=1 AND s.day='J' AND s.class_id IS NOT NULL LIMIT 1;

-- AP12: Pedro ausente J período 2 → PENDIENTE (HOY)
INSERT INTO substitutions (absence_detail_id, schedule_id, absent_teacher_id, class_id, date, status, enabled)
SELECT 12, s.id, 6, s.class_id, '2026-05-28', 'PENDIENTE', 1
FROM schedules s WHERE s.teacher_id=6 AND s.period_id=2 AND s.day='J' AND s.class_id IS NOT NULL LIMIT 1;

-- AP13: Isabel ausente L período 2 → PENDIENTE (2-jun)
INSERT INTO substitutions (absence_detail_id, schedule_id, absent_teacher_id, class_id, date, status, enabled)
SELECT 13, s.id, 7, s.class_id, '2026-06-02', 'PENDIENTE', 1
FROM schedules s WHERE s.teacher_id=7 AND s.period_id=2 AND s.day='L' AND s.class_id IS NOT NULL LIMIT 1;

-- AP14: Isabel ausente L período 3 → PENDIENTE (2-jun)
INSERT INTO substitutions (absence_detail_id, schedule_id, absent_teacher_id, class_id, date, status, enabled)
SELECT 14, s.id, 7, s.class_id, '2026-06-02', 'PENDIENTE', 1
FROM schedules s WHERE s.teacher_id=7 AND s.period_id=3 AND s.day='L' AND s.class_id IS NOT NULL LIMIT 1;

-- =============================================================
-- EVENTOS
-- =============================================================
INSERT INTO events (id, title, description, start_date, end_date, enabled) VALUES
(1, 'Excursión 2-DAW Madrid',     'Visita cultural a museos tecnológicos de Madrid. Salida 8:00 h desde el centro.',    '2026-06-05', '2026-06-05', 1),
(2, 'Jornada formación docente',  'Formación permanente del profesorado. Aula 2.1, de 9:00 a 14:00 h.',                 '2026-06-10', '2026-06-10', 1),
(3, 'Olimpiada informática',      'Participación en olimpiada regional de informática. Centro anfitrión. Pendiente confirmar.', '2026-06-17', '2026-06-17', 0);

-- Profesores asistentes al evento 1 (excursión 2-DAW)
INSERT INTO event_teachers (event_id, teacher_id) VALUES (1, 1), (1, 6), (1, 7);

-- Clases/períodos afectados por el evento 1 (clases de 2-DAW el jueves)
INSERT INTO event_schedules (event_id, schedule_id)
SELECT 1, s.id FROM schedules s WHERE s.class_id = 1 AND s.day = 'J';
