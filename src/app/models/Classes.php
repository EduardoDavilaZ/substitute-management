<?php
    final class Classes extends Model
    {
        public function getClasses() : array
        {
            $res = $this->all('classes');
            return $res['data'] ?? [];
        }

        public function getClass(int $id) : array
        {
            $res = $this->find('classes', $id);
            return $res['success'] ? $res['data'] : [];
        }
        
        public function countActiveClasses() : int
        {
            $res = $this->query("SELECT COUNT(*) as count FROM classes WHERE enabled = 1");
            return $res['success'] ? (int)$res['data'][0]['count'] : 0;
        }
    }
?>