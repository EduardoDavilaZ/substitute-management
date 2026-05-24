<?php

final class Period extends Model
{
    public function getPeriods(): array
    {
        return ($this->all('periods'))['data'] ?? [];
    }

    public function getTeachingPeriods(): array
    {
        $res = $this->query(
                "SELECT id, name, start_time, end_time
                FROM periods
                WHERE LOWER(name) NOT LIKE '%recreo%'
                ORDER BY start_time ASC"
        );

        return $res['data'] ?? [];
    }

    public function getPeriod(int $id): array
    {
        return ($this->find('periods', $id))['data'] ?? [];
    }
}
