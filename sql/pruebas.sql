-- PASO 1: El profesor Alberto (ID 2) notifica su ausencia
INSERT INTO absences (teacher_id, reason, date, proof_file_path) 
VALUES (2, 'Cita médica especialista', '2026-04-13', '/uploads/docs/justificante_alberto.pdf');


-- PASO 2: Detallar las horas afectadas (obtenidas de su horario: Lunes, horas 1 y 2)
-- Se incluye el material que el profesor de guardia usará con los alumnos.
INSERT INTO absence_period (absence_id, period_id, instruction_material_url) 
VALUES (LAST_INSERT_ID(), 1, 'https://drive.google.com/s/clase_2DAW_parte1'),
       (LAST_INSERT_ID(), 2, 'https://drive.google.com/s/clase_2DAW_parte2');

-- REQUERIMIENTO: Mostrar notificaciones de ausencias pendientes de revisar por el Admin
SELECT a.id, t.full_name, a.reason, a.date 
FROM absences a
JOIN teachers t ON a.teacher_id = t.id
WHERE a.viewed = FALSE;


-- PASO 3: Crear las entradas en la tabla de sustituciones
-- Guardamos el 'class_id' y 'absent_teacher_id' directamente para que, 
-- aunque el horario cambie el año que viene, el historial de este día sea real.
-- Transforma una ausencia (una intención de no venir) en una sustitución (una tarea pendiente para el administrador).
INSERT INTO substitutions (absence_detail_id, schedule_id, absent_teacher_id, class_id, date)
SELECT ap.id, s.id, a.teacher_id, s.class_id, a.date 
FROM absence_period ap
JOIN absences a ON ap.absence_id = a.id
JOIN schedules s ON s.teacher_id = a.teacher_id 
    AND s.period_id = ap.period_id
    -- Mapeamos automáticamente el día de la semana de la fecha (date) 
    -- al formato 'L', 'M', 'X', 'J', 'V' del horario (schedule)
    AND s.day = (
        CASE DAYOFWEEK(a.date)
            WHEN 2 THEN 'L'
            WHEN 3 THEN 'M'
            WHEN 4 THEN 'X'
            WHEN 5 THEN 'J'
            WHEN 6 THEN 'V'
        END
    )
WHERE ap.is_cover_generated = FALSE -- generar todas las filas de sustituciones de golpe
AND s.class_id IS NOT NULL; -- que no se genere una sustitución en una hora de guardia 
-- Marcamos las horas de la ausencia como procesadas

UPDATE absence_period SET is_cover_generated = TRUE WHERE is_cover_generated = FALSE;


-- DESPLEGABLE A: ¿Qué clases necesitan un profesor ahora? (Lunes, 1ª Hora)
SELECT sub.id AS sub_id, c.code AS class_code, t.full_name AS absent_teacher
FROM substitutions sub
JOIN teachers t ON sub.absent_teacher_id = t.id
JOIN classes c ON sub.class_id = c.id
WHERE sub.date = '2026-04-13' AND sub.substitute_teacher_id IS NULL;

-- DESPLEGABLE B: Profesores disponibles (Ordenados por prioridad de Guardia)
-- Muestra el contador de guardias realizadas para equilibrar la carga de trabajo.
SELECT 
	t.id, 
	t.full_name, 
	t.substitution_counter,
	CASE 
		WHEN s.class_id IS NULL THEN '1. EN GUARDIA (Por Horario)'
		WHEN ev.id IS NOT NULL THEN '2. DISPONIBLE (Libre por Evento)'
		ELSE '3. OCUPADO (Dando clase)'
	END AS availability
FROM teachers t
JOIN schedules s ON t.id = s.teacher_id AND s.day = 'L' AND s.period_id = 1
LEFT JOIN event_schedules es ON s.id = es.schedule_id
LEFT JOIN events ev ON es.event_id = ev.id AND ev.date = '2026-04-13'
ORDER BY availability ASC, t.substitution_counter ASC;
		
		
-- El Admin asigna a Ernesto (ID 3) para cubrir la clase.
UPDATE substitutions 
SET substitute_teacher_id = 3, 
    status = 'CONFIRMADO'
WHERE id = 1;

-- IMPORTANTE: Incrementamos el contador de guardias del profesor sustituto
UPDATE teachers SET substitution_counter = substitution_counter + 1 WHERE id = 3;


-- Datos para el gráfico de "Guardias por día de la semana"
SELECT DAYNAME(date) as day_name, COUNT(*) as total 
FROM substitutions 
WHERE date BETWEEN '2026-04-13' AND '2026-04-19'
GROUP BY date;

-- Listado para exportar Historial con filtros
-- El SNAPSHOT permite ver quién faltó aunque el profesor ya no esté en el centro.
SELECT s.date, t_sub.full_name as substitute, t_abs.full_name as absent, c.code as class
FROM substitutions s
LEFT JOIN teachers t_sub ON s.substitute_teacher_id = t_sub.id
JOIN teachers t_abs ON s.absent_teacher_id = t_abs.id
JOIN classes c ON s.class_id = c.id
WHERE c.stage = 'CFGS' AND YEAR(s.date) = 2026;





-- Consulta para que el profesor vea qué guardias tiene que hacer hoy
SELECT p.start_time, c.code as class_to_cover, ap.instruction_material_url, t_abs.full_name as covering_for
FROM substitutions s
JOIN absence_period ap ON s.absence_detail_id = ap.id
JOIN periods p ON ap.period_id = p.id
JOIN classes c ON s.class_id = c.id
JOIN teachers t_abs ON s.absent_teacher_id = t_abs.id
WHERE s.substitute_teacher_id = 3 -- ID del profesor logueado
  AND s.date = CURDATE()
  AND s.status = 'CONFIRMADO';
  
  
  
  
  
  
  -- 1. Crear el evento
INSERT INTO events (description, date) VALUES ('Excursión 2-DAW a Madrid', '2026-04-13');

-- 2. Asociar el horario de la clase 1 (2-DAW) a ese evento
-- Todos los profesores que daban clase a 2-DAW ese lunes quedarán "libres" en el desplegable.
INSERT INTO event_schedules (event_id, schedule_id)
SELECT LAST_INSERT_ID(), id 
FROM schedules 
WHERE class_id = 1 AND day = 'L';
