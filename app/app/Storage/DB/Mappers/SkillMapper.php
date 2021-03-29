<?php


namespace App\Storage\DB\Mappers;

use App\Collections\SkillCollection;
use App\Models\DTOs\SkillDTO;
use PDO;
use PDOStatement;
use App\Models\Skill;

class SkillMapper extends Mapper
{

    private PDOStatement $selectNameStmt;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);

        $this->selectStmt = $pdo->prepare(
            'SELECT id, name FROM skills WHERE id = ?'
        );

        $this->selectNameStmt = $pdo->prepare(
            'SELECT id, name FROM skills WHERE name = ?'
        );

        $this->insertStmt = $pdo->prepare(
            'insert into skills (name) values (?)'
        );

        $this->updateStmt = $pdo->prepare(
            'update skills set name = ? WHERE id = ?'
        );

        $this->deleteStmt = $pdo->prepare('delete FROM skills WHERE id = ?');

        $this->batchStmt = $pdo->prepare(
            'select  id, name FROM skills order by id DESC limit ?  offset ?'
        );
    }

    public function findById(int $id): ?Skill
    {
        if ($result = $this->findByOneField($this->selectStmt, $id, false)) {
            return new Skill($result['id'], $result['name']);
        }
        return null;
    }

//    public function findIN(array $data): ?SkillCollection
//    {
//        $query = 'SELECT id, name FROM skills WHERE id IN (' . implode('?,')')';
//        $stmt = $this->pdo->prepare(
//            $query
//        );
//    }

    public function findByName(string $name): ?Skill
    {
        if ($result = $this->findByOneField($this->selectStmt, $name, false)) {
            return new Skill($result['id'], $result['name']);
        }
        return null;
    }

    public function insert(SkillDTO $skillDTO): Skill
    {
        $id = $this->baseInsert([$skillDTO->name], 'skills_id_seq');
        return new Skill($id, $skillDTO->name);
    }

    public function update(Skill $skill): bool
    {
        $id = $skill->getId();
        return $this->baseUpdate([$skill->getName(), $id]);
    }

    public function delete(int $skillID): bool
    {
        return $this->baseDelete([$skillID]);
    }

    public function getBatch(int $limit = 5, int $offset = 0): ?SkillCollection
    {
        $result = $this->baseGetBatch($limit, $offset);
        if (empty($result)) {
            return null;
        }
        return new SkillCollection($result);
    }
}