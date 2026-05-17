<?php

final class Event extends Model
{
    public function getEvents() : array
    {
        return ($this->all('events'))['data'];
    }

    public function getEvent(int $id): array
    {
        $res = $this->find('events', $id);
        if (!$res['success'] || empty($res['data'])) { return []; }
        return $res['data'];
    }

    public function getEventsWithClasses() : array
    {
        $sql = "SELECT 
                    e.id AS event_id, e.title, e.description, e.start_date, e.end_date,
                    c.id AS class_id, c.code AS class_code, c.name AS class_name
                FROM events e
                LEFT JOIN event_schedules es ON e.id = es.event_id
                LEFT JOIN schedules s ON es.schedule_id = s.id
                LEFT JOIN classes c ON s.class_id = c.id
                ORDER BY e.start_date DESC";

        return ($this->queryNested($sql, [], [
            'event_id' => [ 'container' => 'affected_classes' ],
            'class_id' => [ 'prefix' => 'class_' ]
        ]))['data'];
    }

    public function getEventWithClassesAndTeachers(int $id): array
    {
        $sqlEvent = "SELECT 
                        e.id AS event_id, e.title, e.description, e.start_date, e.end_date,
                        c.id AS class_id, c.code AS class_code, c.name AS class_name,
                        p.id AS period_id, p.name AS period_name
                    FROM events e
                    LEFT JOIN event_schedules es ON e.id = es.event_id
                    LEFT JOIN schedules s ON es.schedule_id = s.id
                    LEFT JOIN classes c ON s.class_id = c.id
                    LEFT JOIN periods p ON s.period_id = p.id
                    WHERE e.id = :id";

        $config = [
            'event_id'  => ['container' => 'affected_classes'],
            'class_id'  => ['container' => 'affected_periods', 'prefix' => 'class_'],
            'period_id' => ['prefix' => 'period_']
        ];

        $res = $this->queryNested($sqlEvent, ['id' => $id], $config);
        
        if (!$res['success'] || empty($res['data'])) {
            return [];
        }
        $eventData = $res['data'][0];

        $sqlTeachers = "SELECT t.id, t.full_name 
                        FROM event_teachers et
                        INNER JOIN teachers t ON et.teacher_id = t.id
                        WHERE et.event_id = :id";
                        
        $resTeachers = $this->query($sqlTeachers, ['id' => $id]);
        
        $eventData['affected_teachers'] = ($resTeachers['success']) ? $resTeachers['data'] : [];

        return $eventData;
    }

    public function saveEvent(array $eventData, array $classIds, array $periodIds, array $teacherIds, int $eventId = 0): bool
    {
        // SQL QUERIES DEFINITION
        $sqlInsertEvent         = "INSERT INTO events (title, description, start_date, end_date) 
                                VALUES (:title, :description, :start_date, :end_date)";
                                
        $sqlUpdateEvent         = "UPDATE events SET title = :title, description = :description, 
                                        start_date = :start_date, end_date = :end_date 
                                WHERE id = :id";
                                
        $sqlDeleteSchedules     = "DELETE FROM event_schedules WHERE event_id = :id";
        
        $sqlInsertSchedule      = "INSERT INTO event_schedules (event_id, schedule_id) 
                                VALUES (:e_id, :s_id)";
                                
        $sqlDeleteEventTeachers = "DELETE FROM event_teachers WHERE event_id = :id";
        
        $sqlInsertEventTeacher  = "INSERT INTO event_teachers (event_id, teacher_id) 
                                VALUES (:e_id, :t_id)";
                                
        $sqlDeleteOldAbsences   = "DELETE FROM absences WHERE reason = :reason";
        
        $sqlInsertAbsence       = "INSERT INTO absences (teacher_id, reason, date, is_justified, viewed) 
                                VALUES (:t_id, :reason, :date, 1, 0)";
                                
        $sqlInsertAbsencePeriod = "INSERT INTO absence_period (absence_id, period_id, comments) 
                                VALUES (:a_id, :p_id, :comments)";
                                
        $sqlDeleteEmptyAbsence  = "DELETE FROM absences WHERE id = :id";

        // Base strings for dynamic associative queries (placeholders injected safely below)
        $sqlFindSchedulesBase   = "SELECT id FROM schedules WHERE class_id IN (%s) AND period_id IN (%s)";
        $sqlCheckScheduleBase   = "SELECT id FROM schedules WHERE teacher_id = :t_id AND day = :day AND period_id = :p_id AND class_id IN (%s)";

        try {
            $this->beginTransaction();

            // STEP 1: SAVE OR UPDATE EVENT CORE DATA
            if ($eventId > 0) {
                $params = array_merge($eventData, ['id' => $eventId]);
                $res = $this->update($sqlUpdateEvent, $params);
                if (!$res['success']) throw new Exception("Failed to update event core details.");
                
                // Preventative cleanup of old automated absences linked to this event's title
                $this->delete($sqlDeleteOldAbsences, ['reason' => "Evento: " . $eventData['title']]);
            } else {
                $res = $this->insert($sqlInsertEvent, $eventData);
                if (!$res['success']) throw new Exception("Failed to insert new event core details.");
                $eventId = (int)$res['lastInsertId'];
            }

            // STEP 2: ASSOCIATE AFFECTED CLASS SCHEDULES WITH THE EVENT
            $this->delete($sqlDeleteSchedules, ['id' => $eventId]);
            
            if (!empty($classIds) && !empty($periodIds)) {
                // Generate associative named placeholders dynamically
                $classPlaceholdersArray = [];
                $queryParams = [];
                foreach ($classIds as $index => $id) {
                    $key = "class_id_" . $index;
                    $classPlaceholdersArray[] = ":" . $key;
                    $queryParams[$key] = $id;
                }

                $periodPlaceholdersArray = [];
                foreach ($periodIds as $index => $id) {
                    $key = "period_id_" . $index;
                    $periodPlaceholdersArray[] = ":" . $key;
                    $queryParams[$key] = $id;
                }

                $classPlaceholders  = implode(',', $classPlaceholdersArray);
                $periodPlaceholders = implode(',', $periodPlaceholdersArray);

                // Build and execute the dynamic associative query
                $sqlFindSchedules = sprintf($sqlFindSchedulesBase, $classPlaceholders, $periodPlaceholders);
                $resSchedules = $this->query($sqlFindSchedules, $queryParams);

                if ($resSchedules['success'] && !empty($resSchedules['data'])) {
                    foreach ($resSchedules['data'] as $sch) {
                        $this->insert($sqlInsertSchedule, ['e_id' => $eventId, 's_id' => $sch['id']]);
                    }
                }
            }

            // STEP 3: ASSOCIATE CHAPERONE TEACHERS AND GENERATE SMART ABSENCES
            $this->delete($sqlDeleteEventTeachers, ['id' => $eventId]);
            
            if (!empty($teacherIds)) {
                // Log chaperone relations
                foreach ($teacherIds as $tId) {
                    $this->insert($sqlInsertEventTeacher, ['e_id' => $eventId, 't_id' => $tId]);
                }

                // Set up an inclusive date loop processing window
                $start = new DateTime($eventData['start_date']);
                $end   = new DateTime($eventData['end_date']);
                $end->modify('+1 day'); 

                $interval  = new DateInterval('P1D');
                $dateRange = new DatePeriod($start, $interval, $end);

                // Mapping PHP day index to database Enum format ('L', 'M', 'X', 'J', 'V')
                $dayMapper = [1 => 'L', 2 => 'M', 3 => 'X', 4 => 'J', 5 => 'V'];

                foreach ($dateRange as $dateObj) {
                    $currentDateStr = $dateObj->format('Y-m-d');
                    $dayOfWeekIndex = (int)$dateObj->format('N'); // 1 (Monday) to 7 (Sunday)

                    // Skip weekends entirely as no school schedules apply
                    if ($dayOfWeekIndex > 5) continue; 
                    
                    $dbDay = $dayMapper[$dayOfWeekIndex];

                    foreach ($teacherIds as $tId) {
                        // Create primary absence logging header record for the teacher on this day
                        $resAbs = $this->insert($sqlInsertAbsence, [
                            't_id'   => $tId,
                            'reason' => "Evento: " . $eventData['title'],
                            'date'   => $currentDateStr
                        ]);

                        if ($resAbs['success'] && !empty($periodIds)) {
                            $absenceId = (int)$resAbs['lastInsertId'];
                            $insertedPeriodsCount = 0;

                            foreach ($periodIds as $pId) {
                                
                                // Check if this teacher is assigned to any of 
                                // the travelling classes during this specific day and class period.
                                if (!empty($classIds)) {
                                    $classPlaceholdersArray = [];
                                    $queryParams = [
                                        't_id' => $tId,
                                        'day'  => $dbDay,
                                        'p_id' => $pId
                                    ];

                                    // Generate unique associative keys for the class filter loop
                                    foreach ($classIds as $index => $id) {
                                        $key = "class_id_" . $index;
                                        $classPlaceholdersArray[] = ":" . $key;
                                        $queryParams[$key] = $id;
                                    }

                                    $classPlaceholders = implode(',', $classPlaceholdersArray);
                                    $sqlCheckSchedule  = sprintf($sqlCheckScheduleBase, $classPlaceholders);
                                    
                                    $resCheck = $this->query($sqlCheckSchedule, $queryParams);

                                    // If teacher is teaching a class that is already on the field trip, 
                                    // no substitution coverage is needed. Skip creating an absence period.
                                    if ($resCheck['success'] && !empty($resCheck['data'])) {
                                        continue; 
                                    }
                                }

                                // If teacher belongs somewhere else during this hour, generate an actionable absence tracking row
                                $this->insert($sqlInsertAbsencePeriod, [
                                    'a_id'     => $absenceId,
                                    'p_id'     => $pId,
                                    'comments' => "Ausencia automatizada por asistencia al evento: " . $eventData['title']
                                ]);
                                $insertedPeriodsCount++;
                            }

                            // Clean up empty parent headers if all of the teacher's hours 
                            // matched travelling classes (meaning they don't leave any stationary classes behind).
                            if ($insertedPeriodsCount === 0) {
                                $this->delete($sqlDeleteEmptyAbsence, ['id' => $absenceId]);
                            }
                        }
                    }
                }
            }

            $this->commit();
            return true;

        } catch (Exception $e) {
            $this->rollBack();
            return false;
        }
    }

    public function deleteEvent(int $id): bool
    {
        $event = $this->getEvent($id);
        if (!empty($event)) {
            $this->delete("DELETE FROM absences WHERE reason = :reason", ['reason' => "Evento: " . $event['title']]);
        }
        
        $res = $this->delete("DELETE FROM events WHERE id = :id", ['id' => $id]);
        return ($res['success'] && ($res['rowsAffected'] ?? 0) > 0);
    }
}