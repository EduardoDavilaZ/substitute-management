<?php

final class Event extends Model
{
    public function getEvents() : array
    {
        return ($this->all('events'))['data'];
    }

    public function getEvent(int $id) : array{
        return ($this->find('events', $id))['data'];
    }

    public function getEventsWithClasses()
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
}

?>