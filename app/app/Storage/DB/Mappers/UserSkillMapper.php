<?php


namespace App\Storage\DB\Mappers;

use App\Models\DTOs\UserSkillDTO;
use App\Models\UserSkill;
use PDOStatement;
use PDO;

class UserSkillMapper extends Mapper
{
    private PDOStatement $selectUserIDStmt;
    private PDOStatement $selectSkillIDStmt;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);

        $this->selectStmt = $pdo->prepare(
            'SELECT id, user_id, skill_id FROM users_skills WHERE id = ?'
        );

        $this->selectUserIDStmt = $pdo->prepare(
            'SELECT id, user_id, skill_id FROM users_skills WHERE user_id = ?'
        );

        $this->selectSkillIDStmt = $pdo->prepare(
            'SELECT id, user_id, skill_id FROM users_skills WHERE skill_id = ?'
        );

        $this->insertStmt = $pdo->prepare(
            'INSERT INTO users_skills (user_id, skill_id) VALUES (?, ?)'
        );

        $this->updateStmt = $pdo->prepare(
            'UPDATE users_skills SET user_id = ?, skill_id = ? WHERE id = ?'
        );

        $this->deleteStmt = $pdo->prepare(
            'DELETE FROM users_skills WHERE id = ?'
        );
    }

    public function findById(int $id): ?UserSkill
    {
        if ($result = $this->findByOneField($this->selectStmt, $id, false)) {
            return $this->fillUserSkill($result);
        }
        return null;
    }

    public function findByUserID(int $userID): ?UserSkill
    {
        if ($result = $this->findByOneField($this->selectUserIDStmt, $userID, true)) {
            return $this->fillUserSkill($result);
        }
        return null;
    }

    public function findBySkillID(int $skillID): ?UserSkill
    {
        if ($result = $this->findByOneField($this->selectSkillIDStmt, $skillID, true)) {
            return $this->fillUserSkill($result);
        }
        return null;
    }

    private function fillUserSkill(array $data): UserSkill
    {
        return new UserSkill(
            $data['id'],
            $data['user_id'],
            $data['skill_id'],
        );
    }

    public function insert(UserSkillDTO $userSkillDTO): UserSkill
    {
        $id = $this->baseInsert(
            [
                $userSkillDTO->userID,
                $userSkillDTO->skillID,
            ],
            'users_skills_id_seq'
        );
        return new UserSkill(
            $id,
            $userSkillDTO->userID,
            $userSkillDTO->skillID,
        );
    }

    public function update(UserSkill $userSkill): bool
    {
        $id = $userSkill->getId();
        return $this->baseUpdate([
            $userSkill->getUserID(),
            $userSkill->getSkillID(),
            $id
        ]);
    }

    public function delete(int $userSkillID): bool
    {
        return $this->baseDelete([$userSkillID]);
    }
}
