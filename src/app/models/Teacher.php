<?php

final class Teacher extends Model
{
    public function getTeachers() : array
    {
        $res = $this->all('teachers');
        return $res['data'] ?? [];
    }

    public function getTeacher(int $id) : array
    {
        $res = $this->find('teachers', $id);
        return $res['success'] ? $res['data'] : [];
    }    
}

?>