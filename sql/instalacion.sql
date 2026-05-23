-- -----------------------------------------------------------------------------
-- 1. INDEPENDENT TABLES
-- -----------------------------------------------------------------------------

CREATE TABLE classes (
    id TINYINT UNSIGNED NOT NULL,
    code CHAR(10) NOT NULL,
    name VARCHAR(50) NOT NULL,
    stage ENUM('ESO', 'BACH', 'CFGM', 'CFGS', 'PRIM') NOT NULL,
	enabled BOOLEAN NOT NULL DEFAULT TRUE,
    CONSTRAINT pk_classes PRIMARY KEY (id)
);

CREATE TABLE periods (
    id TINYINT UNSIGNED NOT NULL,
	name CHAR(7) NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    CONSTRAINT pk_periods PRIMARY KEY (id)
);

CREATE TABLE teachers (
    id SMALLINT UNSIGNED NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) NULL UNIQUE,
    password_hash CHAR(60) NOT NULL,
    profile_img_path VARCHAR(255) NULL,
    substitution_counter TINYINT UNSIGNED DEFAULT 0,
    is_tutor BOOLEAN default 0,
	enabled BOOLEAN NOT NULL DEFAULT TRUE,
    CONSTRAINT pk_teachers PRIMARY KEY (id)
);

-- -----------------------------------------------------------------------------
-- 2. CORE SCHEDULE & EVENTS
-- -----------------------------------------------------------------------------

CREATE TABLE schedules (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    class_id TINYINT UNSIGNED NULL,
    teacher_id SMALLINT UNSIGNED NOT NULL,
    period_id TINYINT UNSIGNED NOT NULL,
    day ENUM('L', 'M', 'X', 'J', 'V') NOT NULL, 
    CONSTRAINT pk_schedules PRIMARY KEY (id),
    CONSTRAINT fk_schedules_class FOREIGN KEY (class_id) REFERENCES classes(id) ON UPDATE CASCADE,
    CONSTRAINT fk_schedules_teacher FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON UPDATE CASCADE,
    CONSTRAINT fk_schedules_period FOREIGN KEY (period_id) REFERENCES periods(id) ON UPDATE CASCADE
);

CREATE TABLE events (
    id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
	title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    start_date DATE NOT NULL,
	end_date DATE NOT NULL,
	enabled BOOLEAN NOT NULL DEFAULT TRUE,
    CONSTRAINT pk_events PRIMARY KEY (id)
);

CREATE TABLE event_schedules (
    event_id SMALLINT UNSIGNED NOT NULL,
    schedule_id INT UNSIGNED NOT NULL,
    CONSTRAINT pk_event_schedules PRIMARY KEY (event_id, schedule_id),
    CONSTRAINT fk_ev_sch_event FOREIGN KEY (event_id) REFERENCES events(id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_ev_sch_schedule FOREIGN KEY (schedule_id) REFERENCES schedules(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE event_teachers (
    event_id SMALLINT UNSIGNED NOT NULL,
    teacher_id SMALLINT UNSIGNED NOT NULL,
    CONSTRAINT pk_event_teachers PRIMARY KEY (event_id, teacher_id),
    CONSTRAINT fk_ev_tch_event FOREIGN KEY (event_id) REFERENCES events(id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_ev_tch_teacher FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON UPDATE CASCADE ON DELETE CASCADE
);

-- -----------------------------------------------------------------------------
-- 3. ABSENCES & SUBSTITUTIONS
-- -----------------------------------------------------------------------------

CREATE TABLE absences (
    id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    teacher_id SMALLINT UNSIGNED NOT NULL,
    reason VARCHAR(255) NOT NULL,
    date DATE NOT NULL,
    proof_file_path VARCHAR(255) NULL,
    is_justified BOOLEAN DEFAULT FALSE,
    viewed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT pk_absences PRIMARY KEY (id),
    CONSTRAINT fk_absences_teacher FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON UPDATE CASCADE
);

CREATE TABLE absence_period (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    absence_id SMALLINT UNSIGNED NOT NULL,
    period_id TINYINT UNSIGNED NOT NULL,
    instruction_material_url VARCHAR(255) NULL,
	comments TEXT NULL,
    is_cover_generated BOOLEAN NOT NULL DEFAULT FALSE,
    CONSTRAINT pk_absence_period PRIMARY KEY (id),
    CONSTRAINT fk_details_absence FOREIGN KEY (absence_id) REFERENCES absences(id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_details_period FOREIGN KEY (period_id) REFERENCES periods(id) ON UPDATE CASCADE
);

CREATE TABLE substitutions (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    absence_detail_id INT UNSIGNED NOT NULL,
    substitute_teacher_id SMALLINT UNSIGNED NULL,
    schedule_id INT UNSIGNED NOT NULL,
    
    -- SNAPSHOTS
    absent_teacher_id SMALLINT UNSIGNED NOT NULL,
    class_id TINYINT UNSIGNED NULL,
    date DATE NOT NULL,
    
    is_notified BOOLEAN DEFAULT FALSE, 
    status ENUM('PENDIENTE', 'CONFIRMADO', 'CANCELADO') DEFAULT 'PENDIENTE',
    enabled BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT pk_substitutions PRIMARY KEY (id),
    CONSTRAINT fk_subst_detail FOREIGN KEY (absence_detail_id) REFERENCES absence_period(id),
    CONSTRAINT fk_subst_teacher FOREIGN KEY (substitute_teacher_id) REFERENCES teachers(id) ON UPDATE CASCADE,
    CONSTRAINT fk_subst_schedule FOREIGN KEY (schedule_id) REFERENCES schedules(id),
    CONSTRAINT fk_subst_absent_teacher FOREIGN KEY (absent_teacher_id) REFERENCES teachers(id),
    CONSTRAINT fk_subst_class FOREIGN KEY (class_id) REFERENCES classes(id)
);