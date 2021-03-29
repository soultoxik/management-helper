<?php


namespace App\Storage\DB\Mappers;

use PDO;
use PDOStatement;
use PDOException;
use Exception;

class Mapper
{
    protected PDO $pdo;
    protected PDOStatement $selectStmt;
    protected PDOStatement $insertStmt;
    protected PDOStatement $updateStmt;
    protected PDOStatement $deleteStmt;
    protected PDOStatement $batchStmt;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    protected function findByOneField(PDOStatement $stmt, $id, $allRows): ?array
    {
        try {
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute([$id]);
            if ($allRows) {
                $result = $stmt->fetchAll();
            } else {
                $result = $stmt->fetch();
            }
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }

        if (empty($result)) {
            return null;
        }

        return $result;
    }

    protected function baseInsert(array $data, string $sequence): ?int
    {
        try {
            $result = $this->insertStmt->execute($data);
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }

        if (empty($result)) {
            throw new Exception('');
        }

        return (int) $this->pdo->lastInsertId($sequence);
    }

    protected function baseUpdate(array $data): bool
    {
        try {
            $result = $this->updateStmt->execute($data);
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }

        return $result;
    }

    protected function baseDelete(array $data): bool
    {
        try {
            $result = $this->deleteStmt->execute($data);
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }

        return $result;
    }

    protected function baseGetBatch(int $limit = 5, int $offset = 0): ?array
    {
        try {
            $this->batchStmt->setFetchMode(PDO::FETCH_ASSOC);
            $this->batchStmt->execute([$limit, $offset]);
            $result = $this->batchStmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
        if (empty($result)) {
            return null;
        }
        return $result;
    }
}
