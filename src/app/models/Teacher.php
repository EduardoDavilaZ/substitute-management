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
        $res = $this->query("SELECT id,full_name,email,phone,substitution_counter,is_tutor FROM teachers WHERE enabled = 1");
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
    
    public function updateTeacher(int $id, string $fullName, string $email, string $phone, int $tutor, ?string $profileImgPath = null) : bool
    {
        $sqlUpdateTeacherWithImg = 'UPDATE teachers SET full_name = ?, email = ?, phone = ?, profile_img_path = ?, is_tutor = ? WHERE id = ?';
        $sqlUpdateTeacher = 'UPDATE teachers SET full_name = ?, email = ?, phone = ?, is_tutor = ? WHERE id = ?';

        if ($profileImgPath !== null) {
            $res = $this->update($sqlUpdateTeacherWithImg, [$fullName, $email, $phone, $profileImgPath, $tutor, $id ]);
        } else {
            $res = $this->update($sqlUpdateTeacher, [$fullName, $email, $phone, $tutor, $id ]);
        }

        return $res['success'] ? true : false;
    }
}