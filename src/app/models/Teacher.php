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
        /**
         * @param string|null $profileImgPath Si se indica, actualiza profile_img_path; si es null, se conserva el valor actual.
         */
        public function updateTeacher(
            int $id,
            string $fullName,
            string $email,
            string $phone,
            ?string $profileImgPath = null
        ): bool {
            if ($profileImgPath !== null) {
                $res = $this->query(
                    'UPDATE teachers SET full_name = ?, email = ?, phone = ?, profile_img_path = ? WHERE id = ?',
                    [$fullName, $email, $phone, $profileImgPath, $id]
                );
            } else {
                $res = $this->query(
                    'UPDATE teachers SET full_name = ?, email = ?, phone = ? WHERE id = ?',
                    [$fullName, $email, $phone, $id]
                );
            }
            return $res['success'] ?? false;
        }
    }
?>