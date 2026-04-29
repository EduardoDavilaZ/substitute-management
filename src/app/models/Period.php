<?php

final class Period extends Model
{
    public function getPeriods() : array
    {
        return $this->all('periods');
    }
}

?>