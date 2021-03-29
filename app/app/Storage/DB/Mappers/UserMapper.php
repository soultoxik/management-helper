<?php


namespace App\Storage\DB\Mappers;

use PDO;
use PDOStatement;
use App\Collections\UserCollection;
use App\Models\User;
use App\Models\DTOs\UserDTO;

class UserMapper extends Mapper
{
    private PDOStatement $selectEmailStmt;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);

        $this->selectStmt = $pdo->prepare(
            'SELECT id, email, first_name, last_name, phone, enabled, ' .
            'teacher, created FROM users WHERE id = ?'
        );

        $this->selectEmailStmt = $pdo->prepare(
            'SELECT id, email, first_name, last_name, phone, enabled, ' .
            'teacher, created FROM users WHERE email = ?'
        );

        $this->insertStmt = $pdo->prepare(
            'INSERT INTO users ' .
            '(email, first_name, last_name, phone, enabled, teacher, created) ' .
            'VALUES (?, ?, ?, ?, ?, ?, ?)'
        );

        $this->updateStmt = $pdo->prepare(
            'UPDATE users SET email = ?, first_name = ?, last_name = ?, ' .
            ' phone = ?, enabled = ?, teacher = ?, created = ? WHERE id = ?'
        );

        $this->deleteStmt = $pdo->prepare(
            'DELETE FROM users WHERE id = ?'
        );

        $this->batchStmt = $pdo->prepare(
            'SELECT  id, email, first_name, last_name, phone, enabled, ' .
            'teacher, created FROM users ORDER BY id DESC LIMIT ?  OFFSET ?'
        );
    }

    public function findById(int $id): ?User
    {
        if ($result = $this->findByOneField($this->selectStmt, $id, false)) {
            return $this->fillUser($result);
        }
        return null;
    }

    public function findByEmail(string $email): ?User
    {
        if ($result = $this->findByOneField($this->selectEmailStmt, $email, false)) {
            return $this->fillUser($result);
        }
        return null;
    }

    private function fillUser(array $data): User
    {
        return new User(
            $data['id'],
            $data['email'],
            $data['first_name'],
            $data['last_name'],
            $data['phone'],
            $data['enabled'],
            $data['teacher'],
            $data['created'],
        );
    }

    public function insert(UserDTO $userDTO): User
    {
        $id = $this->baseInsert(
            [
                $userDTO->email,
                $userDTO->firstName,
                $userDTO->lastName,
                $userDTO->phone,
                $userDTO->enabled,
                $userDTO->teacher,
                time()
            ],
            'users_id_seq'
        );
        return new User(
            $id,
            $userDTO->email,
            $userDTO->firstName,
            $userDTO->lastName,
            $userDTO->phone,
            $userDTO->enabled,
            $userDTO->teacher,
            time()
        );
    }

    public function update(User $user): bool
    {
        $id = $user->getId();
        return $this->baseUpdate([
            $user->getEmail(),
            $user->getFirstName(),
            $user->getLastName(),
            $user->getPhone(),
            $user->isEnabled(),
            $user->isTeacher(),
            $user->getCreated(),
            $id
        ]);
    }

    public function delete(int $userID): bool
    {
        return $this->baseDelete([$userID]);
    }

    public function getBatch(int $limit = 5, int $offset = 0): ?UserCollection
    {
        $result = $this->baseGetBatch($limit, $offset);
        if (empty($result)) {
            return null;
        }
        return new UserCollection($result);
    }

    public function findByStatus(bool $status): ?UserCollection
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, email, first_name, last_name, phone, enabled, ' .
            'teacher, created FROM users WHERE status = ?'
        );
        return new UserCollection(
            $this->findByOneField($stmt, $status, true)
        );
    }

    public function findByTeacher(bool $teacher): ?UserCollection
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, email, first_name, last_name, phone, enabled, ' .
            'teacher, created FROM users WHERE teacher = ?'
        );

        return new UserCollection(
            $this->findByOneField($stmt, $teacher, true)
        );
    }
}
