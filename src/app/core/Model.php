<?php

require_once __DIR__ . '/Database.php';

abstract class Model
{
    protected PDO $connection;

    public function __construct()
    {
        $this->connection = Database::getConnection();
    }

    protected function query(string $sql, array $params = [], bool $fetchAll = true): array
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);

            $data = $fetchAll 
                    ? $stmt->fetchAll(PDO::FETCH_ASSOC) 
                    : $stmt->fetch(PDO::FETCH_ASSOC);
            
            $total = $fetchAll ? count($data) : ($data ? 1 : 0);

            return [
                'success' => true, 
                'data' => $data,
                'total' => $total
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ];
        }
    }

    protected function insert(string $sql, array $params = []): array
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            $lastId = $this->connection->lastInsertId();
            return [
                'success' => true, 
                'lastInsertId' => $lastId
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ];
        }
    }

    protected function update(string $sql, array $params = []): array
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return [
                'success' => true, 
                'rowsAffected' => $stmt->rowCount()
            ];
        } catch (PDOException $e) {
            return [
                'success' => false, 
                'code' => $e->getCode(), 
                'message' => $e->getMessage()
            ];
        }
    }
    
    protected function all(string $table): array
    {
        return $this->query("SELECT * FROM `$table`");
    }

    protected function find(string $table, int $id, string $primaryKey = 'id'): array
    {
        return $this->query("SELECT * FROM `$table` WHERE `$primaryKey` = :id LIMIT 1", ['id' => $id], false);
    }

    protected function delete(string $sql, array $params = []): array
    {
        return $this->update($sql, $params);
    }

    protected function beginTransaction() 
    { 
        $this->connection->beginTransaction(); 
    }

    protected function commit() 
    { 
        $this->connection->commit(); 
    }

    protected function rollBack() 
    { 
        $this->connection->rollBack(); 
    }
}
?>