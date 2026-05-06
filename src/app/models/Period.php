<?php

final class Period extends Model
{
    public function getPeriods() : array
    {
        return ($this->all('periods'))['data'];
    }

    public function getPeriod(int $id) : array{
        return ($this->find('periods', $id))['data'];
    }
}

?>