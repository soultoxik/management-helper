<?php


namespace App\Storage\DB\Mappers;

use PDO;
use PDOStatement;
use App\Collections\TeacherConditionsCollection;
use App\Models\DTOs\TeacherConditionsDTO;
use App\Models\TeacherConditions;

class TeacherConditionsMapper extends Mapper
{
    private PDOStatement $selectUserIDStmt;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);

        $this->selectStmt = $pdo->prepare(
            'SELECT id, user_id, max_groups_num, min_group_size, ' .
            ' max_group_size FROM teachers_conditions WHERE id = ?'
        );

        $this->selectUserIDStmt = $pdo->prepare(
            'SELECT id, user_id, max_groups_num, min_group_size, ' .
            ' max_group_size FROM teachers_conditions WHERE user_id = ?'
        );

        $this->insertStmt = $pdo->prepare(
            'INSERT INTO teachers_conditions ' .
            '(user_id, max_groups_num, min_group_size, max_group_size) ' .
            'VALUES (?, ?, ?, ?)'
        );

        $this->updateStmt = $pdo->prepare(
            'UPDATE teachers_conditions SET ' .
            'user_id = ?, max_groups_num = ?, min_group_size = ?, max_group_size = ?' .
            ' WHERE id = ?'
        );

        $this->deleteStmt = $pdo->prepare(
            'DELETE FROM teachers_conditions WHERE id = ?'
        );

        $this->batchStmt = $pdo->prepare(
            'SELECT id, user_id, max_groups_num, min_group_size, ' .
            'max_group_size FROM teachers_conditions order BY id DESC LIMIT ? OFFSET ?'
        );
    }

    public function findById(int $id): ?TeacherConditions
    {
        if ($result = $this->findByOneField($this->selectStmt, $id, false)) {
            return $this->fillTeacherConditions($result);
        }
        return null;
    }

    public function findByUserId(int $userID): ?TeacherConditions
    {
        if ($result = $this->findByOneField($this->selectUserIDStmt, $userID, false)) {
            return $this->fillTeacherConditions($result);
        }
        return null;
    }

    private function fillTeacherConditions(array $data): TeacherConditions
    {
        return new TeacherConditions(
            $data['id'],
            $data['user_id'],
            $data['max_groups_num'],
            $data['min_group_size'],
            $data['max_group_size'],
        );
    }

    public function insert(TeacherConditionsDTO $teacherConditionsDTO): TeacherConditions
    {
        $id = $this->baseInsert(
            [
                $teacherConditionsDTO->id,
                $teacherConditionsDTO->userID,
                $teacherConditionsDTO->maxGroupsNum,
                $teacherConditionsDTO->minGroupSize,
                $teacherConditionsDTO->maxGroupSize
            ],
            'teachers_conditions_id_seq'
        );
        return new TeacherConditions(
            $id,
            $teacherConditionsDTO->userID,
            $teacherConditionsDTO->maxGroupsNum,
            $teacherConditionsDTO->minGroupSize,
            $teacherConditionsDTO->maxGroupSize
        );
    }

    public function update(TeacherConditions $teacherConditions): bool
    {
        $id = $teacherConditions->getId();
        return $this->baseUpdate(
            [
                $teacherConditions->getUserID(),
                $teacherConditions->getMaxGroupsNum(),
                $teacherConditions->getMinGroupSize(),
                $teacherConditions->getMaxGroupSize(),
                $id
            ]
        );
    }

    public function delete(int $teacherConditionsID): bool
    {
        return $this->baseDelete([$teacherConditionsID]);
    }

    public function getBatch(int $limit = 5, int $offset = 0): ?TeacherConditionsCollection
    {
        $result = $this->baseGetBatch($limit, $offset);
        if (empty($result)) {
            return null;
        }
        return new TeacherConditionsCollection($result);
    }
}
