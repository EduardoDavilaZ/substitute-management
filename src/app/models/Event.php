<?php

final class Event extends Model
{
    public function getEvents() : array
    {
        return $this->all('events');
    }

    public function getEvent(int $id) : array{
        return ($this->find('event', $id))['data'];
    }
}

?>