<?php

require_once __DIR__ . '/../config/database.php';

abstract class Model
{
    protected $connection;

    public function __construct()
    {
        $dsn = DB_CONNECTION . ":host=" . DB_HOST . ";dbname=" . DB_DATABASE . ";charset=utf8mb4;";
        try{
            $this->connection = new PDO($dsn, DB_USERNAME, DB_PASSWORD);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e){
            die("Error en la conexión: " . $e->getMessage());
        }
    }

    public function __destruct()
    {
        $this->connection = null;
    }

    protected function query(string $sql, array $params = [], bool $fetchAll = true): array
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);

            if ($fetchAll) {
                return ['success' => true, 'data' => $stmt->fetchAll()];
            } else {
                return ['success' => true, 'data' => $stmt->fetch()];
            }
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
            return ['success' => true, 'rowsAffected' => $stmt->rowCount()];
        } catch (PDOException $e) {
            return ['success' => false, 'code' => $e->getCode(), 'message' => $e->getMessage()];
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