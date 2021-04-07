<?php


namespace App\Repository;

use App\Models\DTOs\SkillDTO;
use App\Models\Skill;
use Illuminate\Support\Collection;

class SkillRepository
{
    public function getSkillByID(int $id): ?Skill
    {
        $skill = Skill::where('id', $id)->first();
        if (empty($skill)) {
            return null;
        }
        return $skill;
    }

    public function getSkillByName(string $name): ?Skill
    {
        $skill = Skill::where('name', $name)->first();
        if (empty($skill)) {
            return null;
        }
        return $skill;
    }

    public function getUserIDsBySkillID(int $skillID): ?array
    {
        try {
            $skill = Skill::where('id', $skillID)->first();
            if (empty($skill)) {
                return null;
            }
            $result = $skill->users;
            return $this->processMultiple($result);
        } catch (\Exception $e) {
        }
    }

    public function getGroupIDsBySkillID(int $skillID): ?array
    {
        try {
            $skill = Skill::where('id', $skillID)->first();
            if (empty($skill)) {
                return null;
            }

            $result = $skill->groups;
            return $this->processMultiple($result);
        } catch (\Exception $e) {
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
            // Exception
        }
    }

    public function update(Skill $newSkill): bool
    {
        try {
            $skill = Skill::where('id', $newSkill->id)->first();
            $skill->name = $newSkill->name;
            return $skill->save();
        } catch (\Exception $e) {
            // Exception
        }
    }

    public function delete(int $id): bool
    {
        try {
            $skill = Skill::where('id', $id)->first();
            if (empty($skill)) {
                return false;
            }
            $skill->delete();
            return true;
        } catch (\Exception $e) {
            // Exception
        }
    }
}
