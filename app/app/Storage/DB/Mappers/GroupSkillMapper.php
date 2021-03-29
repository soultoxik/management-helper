<?php


namespace App\Storage\DB\Mappers;

use App\Models\DTOs\GroupSkillDTO;
use App\Models\GroupSkill;
use PDOStatement;
use PDO;

class GroupSkillMapper extends Mapper
{
    private PDOStatement $selectGroupIDStmt;
    private PDOStatement $selectSkillIDStmt;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);

        $this->selectStmt = $pdo->prepare(
            'SELECT id, group_id, skill_id FROM groups_skills WHERE id = ?'
        );

        $this->selectGroupIDStmt = $pdo->prepare(
            'SELECT id, group_id, skill_id FROM groups_skills WHERE group_id = ?'
        );

        $this->selectSkillIDStmt = $pdo->prepare(
            'SELECT id, group_id, skill_id FROM groups_skills WHERE skill_id = ?'
        );

        $this->insertStmt = $pdo->prepare(
            'INSERT INTO groups_skills (group_id, skill_id) VALUES (?, ?)'
        );

        $this->updateStmt = $pdo->prepare(
            'UPDATE groups_skills SET group_id = ?, skill_id = ? WHERE id = ?'
        );

        $this->deleteStmt = $pdo->prepare(
            'DELETE FROM groups_skills WHERE id = ?'
        );
    }

    public function findById(int $id): ?GroupSkill
    {
        if ($result = $this->findByOneField($this->selectStmt, $id, false)) {
            return $this->fillGroupSkill($result);
        }
        return null;
    }

    public function findByGroupID(int $groupID): ?GroupSkill
    {
        if ($result = $this->findByOneField($this->selectGroupIDStmt, $groupID, true)) {
            return $this->fillGroupSkill($result);
        }
        return null;
    }

    public function findBySkillID(int $skillID): ?GroupSkill
    {
        if ($result = $this->findByOneField($this->selectSkillIDStmt, $skillID, true)) {
            return $this->fillGroupSkill($result);
        }
        return null;
    }

    private function fillGroupSkill(array $data): GroupSkill
    {
        return new GroupSkill(
            $data['id'],
            $data['group_id'],
            $data['skill_id'],
        );
    }

    public function insert(GroupSkillDTO $groupSkillDTO): GroupSkill
    {
        $id = $this->baseInsert(
            [
                $groupSkillDTO->groupID,
                $groupSkillDTO->skillID,
            ],
            'groups_skills_id_seq'
        );
        return new GroupSkill(
            $id,
            $groupSkillDTO->groupID,
            $groupSkillDTO->skillID,
        );
    }

    public function update(GroupSkill $groupSkill): bool
    {
        $id = $groupSkill->getId();
        return $this->baseUpdate([
            $groupSkill->getGroupID(),
            $groupSkill->getSkillID(),
            $id
        ]);
    }

    public function delete(int $groupSkillID): bool
    {
        return $this->baseDelete([$groupSkillID]);
    }
}