<?php

final class Classes extends Model
{
    public function getClasses() : array
    {
        return ($this->all('classes'))['data'];
    }
}

?>