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
        
        if (!$res['success'] || empty($res['data'])) {
            return []; 
        }

        return $res['data'];
    }

    public function getEventsWithClasses() : array
    {
        $sql = "SELECT 
                    e.id AS event_id, 
                    e.title, 
                    e.description, 
                    e.start_date, 
                    e.end_date,
                    c.id AS class_id, 
                    c.code AS class_code, 
                    c.name AS class_name
                FROM events e
                LEFT JOIN event_schedules es ON e.id = es.event_id
                LEFT JOIN schedules s ON es.schedule_id = s.id
                LEFT JOIN classes c ON s.class_id = c.id
                ORDER BY e.start_date DESC";

        return ($this->queryNested($sql, [], [
            'event_id' => [
                'container' => 'affected_classes'
            ],
            'class_id' => [
                'prefix' => 'class_'
            ]
        ]))['data'];
    }

    public function getEventWithClasses(int $id): array
    {
        $sql = "SELECT 
                    e.id AS event_id, e.title, e.description, e.start_date, e.end_date,
                    c.id AS class_id, c.code AS class_code, c.name AS class_name,
                    p.id AS period_id, p.name AS period_name
                FROM events e
                LEFT JOIN event_schedules es ON e.id = es.event_id
                LEFT JOIN schedules s ON es.schedule_id = s.id
                LEFT JOIN classes c ON s.class_id = c.id
                LEFT JOIN periods p ON s.period_id = p.id
                WHERE e.id = :id";

        return ($this->queryNested($sql, ['id' => $id], [
            'event_id' => [
                'container' => 'affected_classes'
            ],
            'class_id' => [
                'container' => 'affected_periods',
                'prefix' => 'class_'
            ],
            'period_id' => [
                'prefix' => 'period_'
            ]
        ]))['data'];
    }

    public function saveEvent(array $eventData, array $classIds, array $periodIds, int $eventId = 0): bool
    {
        $sqlInsertEvent =   "INSERT INTO events (title, description, start_date, end_date) 
                            VALUES (:title, :description, :start_date, :end_date)";
        
        $sqlUpdateEvent =   "UPDATE events SET title = :title, description = :description, 
                            start_date = :start_date, end_date = :end_date WHERE id = :id";
        
        $sqlDeleteRelations =   "DELETE FROM event_schedules WHERE event_id = :id";
        
        $sqlInsertRelation =    "INSERT INTO event_schedules (event_id, schedule_id) VALUES (:e_id, :s_id)";

        try {
            $this->beginTransaction();

            // 1. INSERT OR UPDATE EVENT
            if ($eventId > 0) {
                $params = array_merge($eventData, ['id' => $eventId]);
                $res = $this->update($sqlUpdateEvent, $params);
                
                if (!$res['success']) throw new Exception("Error updating event.");
            } else {
                $res = $this->insert($sqlInsertEvent, $eventData);
                
                if (!$res['success']) throw new Exception("Error inserting event.");
                $eventId = (int)$res['lastInsertId'];
            }

            // 2. CLEAR EXISTING RELATIONS
            $this->delete($sqlDeleteRelations, ['id' => $eventId]);

            // 3. SEARCH FOR SCHEDULE_IDS MATCHING CLASSES AND PERIODS
            if (!empty($classIds) && !empty($periodIds)) {
                // Prepare placeholders for IN clauses (PDO does not accept arrays directly)
                $classPlaceholders = implode(',', array_fill(0, count($classIds), '?'));
                $periodPlaceholders = implode(',', array_fill(0, count($periodIds), '?'));

                // Dynamic SQL for searching schedules
                $sqlSchedules = "SELECT id FROM schedules 
                                WHERE class_id IN ($classPlaceholders) 
                                AND period_id IN ($periodPlaceholders)";
                
                // Execute manual query by merging parameters
                $searchParams = array_merge($classIds, $periodIds);
                $resSchedules = $this->query($sqlSchedules, $searchParams);

                if ($resSchedules['success'] && !empty($resSchedules['data'])) {
                    // 4. LINK NEW SCHEDULES
                    foreach ($resSchedules['data'] as $sch) {
                        $this->insert($sqlInsertRelation, [
                            'e_id' => $eventId,
                            's_id' => $sch['id']
                        ]);
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
        $res = $this->delete("DELETE FROM events WHERE id = :id", ['id' => $id]);
        return ($res['success'] && ($res['rowsAffected'] ?? 0) > 0);
    }
}

?>