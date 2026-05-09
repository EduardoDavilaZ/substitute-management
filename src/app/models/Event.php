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
                e.id AS event_id, 
                e.title, 
                e.description, 
                e.start_date, 
                e.end_date,
                c.id AS class_id, 
                c.code AS class_code, 
                p.id AS period_id  -- Usamos este nombre claro
            FROM events e
            LEFT JOIN event_schedules es ON e.id = es.event_id
            LEFT JOIN schedules s ON es.schedule_id = s.id
            LEFT JOIN classes c ON s.class_id = c.id
            LEFT JOIN periods p ON s.period_id = p.id
            WHERE e.id = :id";

    $res = $this->queryNested($sql, ['id' => $id], [
        'event_id' => [
            'container' => 'affected_classes' 
        ],
        'class_id' => [
            'prefix' => 'class_'
        ],
        // Quitamos el prefijo 'period_' aquí para evitar líos
        'period_id' => [
            'container' => 'affected_periods'
        ]
    ]);

    return ($res['success'] && !empty($res['data'])) ? $res['data'][0] : [];
}

    public function createEvent(array $event, array $scheduleIds) : bool
    {
        try {
            $this->beginTransaction();

            $sqlEvent = "INSERT INTO events (title, description, start_date, end_date) 
                        VALUES (:title, :description, :start_date, :end_date)";
            
            $paramsEvent = [
                'title'       => $event['title'],
                'description' => $event['description'],
                'start_date'  => $event['start_date'],
                'end_date'    => $event['end_date']
            ];

            $resEvent = $this->insert($sqlEvent, $paramsEvent);

            if (!$resEvent['success']) {
                throw new Exception("Error al crear el evento principal.");
            }

            $eventId = $resEvent['lastInsertId'];

            $sqlRelation = "INSERT INTO event_schedules (event_id, schedule_id) VALUES (:event_id, :schedule_id)";
            
            foreach ($scheduleIds as $scheduleId) {
                $resRelation = $this->insert($sqlRelation, [
                    'event_id'    => $eventId,
                    'schedule_id' => $scheduleId
                ]);

                if (!$resRelation['success']) {
                    throw new Exception("Error al vincular el horario ID: $scheduleId");
                }
            }

            $this->commit();
            return true;

        } catch (Exception $e) {
            $this->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }

    public function updateEvent(int $schedule_id, int $teacher_id) : bool
    {
        $sql = "UPDATE schedules SET teacher_id = :teacher_id WHERE id = :id";
        
        $res = $this->update($sql, [
            'teacher_id' => $teacher_id, 
            'id'         => $schedule_id
        ]);

        if (!($res['success'] ?? false)) {
            return false;
        }
        return true; 
    }
    public function deleteEvent(int $id) : bool
    {
        $sql = "DELETE FROM schedules WHERE id = :id";
        $res = $this->delete($sql, ['id' => $id]);

        if (!($res['success'] ?? false)) {
            return false;
        }
        
        return ($res['rowsAffected'] ?? 0) > 0;
    }
}

?>