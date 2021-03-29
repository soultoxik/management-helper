<?php


namespace App\Storage\DB\Mappers;

use App\Models\DTOs\GroupUserDTO;
use App\Models\GroupUser;
use PDOStatement;
use PDO;

class GroupUserMapper extends Mapper
{
    private PDOStatement $selectGroupIDStmt;
    private PDOStatement $selectUserIDStmt;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);

        $this->selectStmt = $pdo->prepare(
            'SELECT id, group_id, user_id FROM groups_users WHERE id = ?'
        );

        $this->selectGroupIDStmt = $pdo->prepare(
            'SELECT id, group_id, user_id FROM groups_users WHERE group_id = ?'
        );

        $this->selectUserIDStmt = $pdo->prepare(
            'SELECT id, group_id, user_id FROM groups_users WHERE user_id = ?'
        );

        $this->insertStmt = $pdo->prepare(
            'INSERT INTO groups_users (group_id, user_id) VALUES (?, ?)'
        );

        $this->updateStmt = $pdo->prepare(
            'UPDATE groups_users SET group_id = ?, user_id = ? WHERE id = ?'
        );

        $this->deleteStmt = $pdo->prepare(
            'DELETE FROM groups_users WHERE id = ?'
        );
    }

    public function findById(int $id): ?GroupUser
    {
        if ($result = $this->findByOneField($this->selectStmt, $id, false)) {
            return $this->fillGroupUser($result);
        }
        return null;
    }

    public function findByGroupID(int $groupID): ?GroupUser
    {
        if ($result = $this->findByOneField($this->selectGroupIDStmt, $groupID, true)) {
            return $this->fillGroupUser($result);
        }
        return null;
    }

    public function findByUserID(int $userID): ?GroupUser
    {
        if ($result = $this->findByOneField($this->selectUserIDStmt, $userID, true)) {
            return $this->fillGroupUser($result);
        }
        return null;
    }

    private function fillGroupUser(array $data): GroupUser
    {
        return new GroupUser(
            $data['id'],
            $data['group_id'],
            $data['user_id'],
        );
    }

    public function insert(GroupUserDTO $groupUserDTO): GroupUser
    {
        $id = $this->baseInsert(
            [
                $groupUserDTO->groupID,
                $groupUserDTO->userID,
            ],
            'groups_users_id_seq'
        );
        return new GroupUser(
            $id,
            $groupUserDTO->groupID,
            $groupUserDTO->userID,
        );
    }

    public function update(GroupUser $groupUser): bool
    {
        $id = $groupUser->getId();
        return $this->baseUpdate([
            $groupUser->getGroupID(),
            $groupUser->getUserID(),
            $id
        ]);
    }

    public function delete(int $id): bool
    {
        return $this->baseDelete([$id]);
    }
}