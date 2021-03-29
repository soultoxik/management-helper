<?php


namespace App\Storage\DB\Mappers;

use App\Models\Student;
use PDO;

class StudentMapper
{
    private PDO $pdo;
    private UserMapper $userMapper;
    private SkillMapper $skillMapper;
    private UserSkillMapper $userSkillMapper;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->userMapper = new UserMapper($pdo);
        $this->skillMapper = new SkillMapper($pdo);
        $this->userSkillMapper = new UserSkillMapper($pdo);
    }

    public function findById(int $id): ?Student
    {
        $user = $this->userMapper->findById($id);
        $userSkillCollection = $this->userSkillMapper->findByUserID($user->getId());
        $skills = [];
        foreach ($userSkillCollection as $item) {
            $skills[] = $item->getSkillID();
        }

        $skillCollection = $this->skillMapper->findIN($skills);
        return new Student($user, $skillCollection);
    }



}
