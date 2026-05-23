<?php

final class Classes extends Model
{
    private const STAGES = ['ESO', 'BACH', 'CFGM', 'CFGS', 'PRIM'];

    public function getClasses(): array
    {
        $res = $this->query("SELECT id, code, name, stage FROM classes WHERE enabled = 1 ORDER BY code ASC");
        return $res['data'] ?? [];
    }

    public function getClassesEnabled(): array
    {
        $res = $this->query(
            "SELECT id, code, name, stage FROM classes WHERE enabled = 1 ORDER BY code ASC"
        );
        return $res['data'] ?? [];
    }

    public function getClass(int $id): array
    {
        $res = $this->find('classes', $id);
        return $res['success'] ? ($res['data'] ?? []) : [];
    }

    public function countActiveClasses(): int
    {
        $res = $this->query("SELECT COUNT(*) as count FROM classes WHERE enabled = 1");
        return $res['success'] ? (int) $res['data'][0]['count'] : 0;
    }

    public function getNextId(): int
    {
        $res = $this->query("SELECT COALESCE(MAX(id), 0) + 1 AS next_id FROM classes", [], false);
        return $res['success'] ? (int) ($res['data']['next_id'] ?? 1) : 1;
    }

    public function codeExists(string $code, int $excludeId = 0): bool
    {
        $sql = "SELECT id FROM classes WHERE code = ? AND id != ? LIMIT 1";
        $res = $this->query($sql, [$code, $excludeId], false);
        return $res['success'] && !empty($res['data']);
    }

    public function createClass(int $id, string $code, string $name, string $stage): bool
    {
        $res = $this->insert(
            "INSERT INTO classes (id, code, name, stage, enabled) VALUES (?, ?, ?, ?, 1)",
            [$id, $code, $name, $stage]
        );
        return $res['success'];
    }

    public function updateClass(int $id, string $code, string $name, string $stage): bool
    {
        $res = $this->update(
            "UPDATE classes SET code = ?, name = ?, stage = ? WHERE id = ? AND enabled = 1",
            [$code, $name, $stage, $id]
        );
        return $res['success'];
    }

    public function deleteClass(int $id): bool
    {
        $res = $this->query("UPDATE classes SET enabled = 0 WHERE id = ?", [$id]);
        return $res['success'];
    }

    public static function validStages(): array
    {
        return self::STAGES;
    }

    public static function isValidStage(string $stage): bool
    {
        return in_array($stage, self::STAGES, true);
    }
}
