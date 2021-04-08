<?php


namespace App\Repository;

use App\Exceptions\SkillRepositoryException;
use App\Models\DTOs\SkillDTO;
use App\Models\Skill;
use Illuminate\Support\Collection;
use League\Route\Http\Exception\NotFoundException;

class SkillRepository
{
    public function getSkillByID(int $id): ?Skill
    {
        $skill = Skill::where('id', $id)->first();
        if (empty($skill)) {
            throw new NotFoundException('Skill (' . $id . ') not found');
        }
        return $skill;
    }

    public function getSkillByName(string $name): ?Skill
    {
        $skill = Skill::where('name', $name)->first();
        if (empty($skill)) {
            throw new NotFoundException('Skill not found, by name' . $name);
        }
        return $skill;
    }

    public function getUserIDsBySkillID(int $id): ?array
    {
        try {
            $skill = Skill::where('id', $id)->first();
            if (empty($skill)) {
                throw new NotFoundException('Skill (' . $id . ') not found');
            }
            $result = $skill->users;
            return $this->processMultiple($result);
        } catch (\Exception $e) {
            throw new SkillRepositoryException($e->getMessage(), $e->getCode());
        }
    }

    public function getGroupIDsBySkillID(int $id): ?array
    {
        try {
            $skill = Skill::where('id', $id)->first();
            if (empty($skill)) {
                throw new NotFoundException('Skill (' . $id . ') not found');
            }

            $result = $skill->groups;
            return $this->processMultiple($result);
        } catch (\Exception $e) {
            throw new SkillRepositoryException($e->getMessage(), $e->getCode());
        }
    }

    private function processMultiple(Collection $data): ?array
    {
        if (empty($data)) {
            return null;
        }
        $ids = [];
        foreach ($data as $item) {
            $ids[] = $item->id;
        }
        if (empty($ids)) {
            return null;
        }
        return $ids;
    }


    public function create(SkillDTO $skillDTO): ?Skill
    {
        try {
            return Skill::create([
                'name' => $skillDTO->name,
            ]);
        } catch (\Exception $e) {
            throw new SkillRepositoryException($e->getMessage(), $e->getCode());
        }
    }

    public function update(Skill $newSkill): bool
    {
        try {
            $skill = Skill::where('id', $newSkill->id)->first();
            $skill->name = $newSkill->name;
            return $skill->save();
        } catch (\Exception $e) {
            throw new SkillRepositoryException($e->getMessage(), $e->getCode());
        }
    }

    public function delete(int $id): bool
    {
        try {
            $skill = Skill::where('id', $id)->first();
            if (empty($skill)) {
                throw new NotFoundException(
                    'Can not delete. Skill (' . $id . ') not found'
                );
            }
            $skill->delete();
            return true;
        } catch (\Exception $e) {
            throw new SkillRepositoryException($e->getMessage(), $e->getCode());
        }
    }
}
