<?php


namespace App\Storage\DB\Mappers;

use App\Collections\GroupCollection;
use App\Models\DTOs\GroupDTO;
use App\Models\Group;
use PDO;
use PDOStatement;

class GroupMapper extends Mapper
{
    private PDOStatement $selectNameStmt;
    private PDOStatement $selectActiveStmt;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);

        $this->selectStmt = $pdo->prepare(
            'SELECT id, name, user_id, min_students_num, max_students_num, ' .
            'min_skills_num, max_skills_num, max_useless_skill_students, enabled, created '
            . 'FROM groups WHERE id = ?'
        );

        $this->selectNameStmt = $pdo->prepare(
            'SELECT id, name, user_id, min_students_num, max_students_num, ' .
            'min_skills_num, max_skills_num, max_useless_skill_students, enabled, created '
            . 'FROM groups WHERE name = ?'
        );

        $this->selectActiveStmt = $pdo->prepare(
            'SELECT id, name, user_id, min_students_num, max_students_num, ' .
            'min_skills_num, max_skills_num, max_useless_skill_students, enabled, created '
            . 'FROM groups WHERE enabled = ?'
        );

        $this->insertStmt = $pdo->prepare(
            'INSERT INTO groups ' .
            '(name, user_id, min_students_num, max_students_num, min_skills_num, ' .
            'max_skills_num, max_useless_skill_students, enabled, created) ' .
            'VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );

        $this->updateStmt = $pdo->prepare(
            'UPDATE groups SET name = ?, user_id = ?, min_students_num = ?, ' .
            ' max_students_num = ?, min_skills_num = ?, max_skills_num = ?, ' .
            'max_useless_skill_students = ?, enabled = ?, created = ? WHERE id = ?'
        );

        $this->deleteStmt = $pdo->prepare(
            'DELETE FROM groups WHERE id = ?'
        );

        $this->batchStmt = $pdo->prepare(
            'SELECT id, name, user_id, min_students_num, max_students_num, ' .
            'min_skills_num, max_skills_num, max_useless_skill_students, enabled, created '
            . 'FROM groups ORDER BY id DESC LIMIT ?  OFFSET ?'
        );
    }

    public function findById(int $id): ?Group
    {
        if ($result = $this->findByOneField($this->selectStmt, $id, false)) {
            return $this->fillGroup($result);
        }
        return null;
    }

    public function findByName(string $name): ?Group
    {
        if ($result = $this->findByOneField($this->selectNameStmt, $name, false)) {
            return $this->fillGroup($result);
        }
        return null;
    }

    public function findByEnabled(bool $enabled): ?Group
    {
        if ($result = $this->findByOneField($this->selectActiveStmt, $enabled, true)) {
            return $this->fillGroup($result);
        }
        return null;
    }

    private function fillGroup(array $data): Group
    {
        return new Group(
            $data['id'],
            $data['name'],
            $data['user_id'],
            $data['min_students_num'],
            $data['max_students_num'],
            $data['min_skills_num'],
            $data['max_skills_num'],
            $data['max_useless_skill_students'],
            $data['enabled'],
            $data['created'],
        );
    }

    public function insert(GroupDTO $groupDTO): Group
    {
        $id = $this->baseInsert(
            [
                $groupDTO->name,
                $groupDTO->userID,
                $groupDTO->minStudentsNum,
                $groupDTO->maxStudentsNum,
                $groupDTO->minSkillsNum,
                $groupDTO->maxSkillsNum,
                $groupDTO->maxUselessSkillStudents,
                $groupDTO->enabled,
                time()
            ],
            'groups_id_seq'
        );
        return new Group(
            $id,
            $groupDTO->name,
            $groupDTO->userID,
            $groupDTO->minStudentsNum,
            $groupDTO->maxStudentsNum,
            $groupDTO->minSkillsNum,
            $groupDTO->maxSkillsNum,
            $groupDTO->maxUselessSkillStudents,
            $groupDTO->enabled,
            time()
        );
    }

    public function update(Group $group): bool
    {
        $id = $group->getId();
        return $this->baseUpdate([
            $group->getName(),
            $group->getUserID(),
            $group->getMinStudentsNum(),
            $group->getMaxStudentsNum(),
            $group->getMinSkillsNum(),
            $group->getMaxSkillsNum(),
            $group->getMaxUselessSkillStudents(),
            $group->isEnabled(),
            $group->getCreated(),
            $id
        ]);
    }

    public function delete(int $userID): bool
    {
        return $this->baseDelete([$userID]);
    }

    public function getBatch(int $limit = 5, int $offset = 0): ?GroupCollection
    {
        $result = $this->baseGetBatch($limit, $offset);
        if (empty($result)) {
            return null;
        }
        return new GroupCollection($result);
    }
}
