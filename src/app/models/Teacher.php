<?php
    final class Teacher extends Model
    {
        public function getTeachers() : array
        {
            $res = $this->all('teachers');
            return $res['data'] ?? [];
        }
        public function getTeachersEnabled() : array
        {
            $res = $this->query("SELECT id,full_name,email,phone,substitution_counter FROM teachers WHERE enabled = 1");
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
        public function deleteTeacher(int $id): bool
        {   
            $res = $this->query("UPDATE teachers SET enabled = 0 WHERE id = ?;",[$id]);
            return $res['success'] ? true : false;
        }
        public function updateTeacher(int $id, array $data): bool
        {
            $fields = [];
            $values = [];

            foreach ($data as $key => $value) {
                $fields[] = "$key = ?";
                $values[] = $value;
            }

            $values[] = $id;
            $query = "UPDATE teachers SET " . implode(', ', $fields) . " WHERE id = ?";
            $res = $this->query($query, $values);
            return $res['success'] ?? false;
        }

        /** True si otro profesor (distinto id) ya usa ese email. */
        public function existsOtherWithEmail(string $email, int $excludeId): bool
        {
            $res = $this->query(
                'SELECT id FROM teachers WHERE email = ? AND id <> ? LIMIT 1',
                [$email, $excludeId],
                false
            );
            return ($res['success'] ?? false) && !empty($res['data']);
        }

        /** True si otro profesor ya usa ese teléfono (mismo valor exacto en BD). */
        public function existsOtherWithPhone(string $phone, int $excludeId): bool
        {
            $res = $this->query(
                'SELECT id FROM teachers WHERE phone = ? AND id <> ? LIMIT 1',
                [$phone, $excludeId],
                false
            );
            return ($res['success'] ?? false) && !empty($res['data']);
        }
    }
?>