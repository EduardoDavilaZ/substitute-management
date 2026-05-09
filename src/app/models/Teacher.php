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
        
        public function countActiveTeachers() : int
        {
            $res = $this->query("SELECT COUNT(*) as count FROM teachers WHERE enabled = 1");
            return $res['success'] ? (int)$res['data'][0]['count'] : 0;
        }
    }
?>