<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DTOs\GroupDTO;

class Group extends Model
{
    use Transaction;

    protected $table = 'groups';
    protected $fillable = [
        'name',
        'user_id',
        'min_students_num',
        'max_students_num',
        'min_skills_num',
        'max_skills_num',
        'max_useless_skill_students',
        'enabled'
    ];

    public function teacher()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function students()
    {
        return $this->belongsToMany('App\Models\User', 'groups_users');
    }

    public function skills()
    {
        return $this->belongsToMany('App\Models\Skill', 'groups_skills');
    }

    /**
     * @param GroupDTO  $group
     * @param array[skill_id] $skills
     *
     * @return Group|null
     */
    public static function insert(GroupDTO $groupDTO, array $skills): ?Group
    {
        try {
            self::beginTransaction();
            $group = Group::create([
                'name' => $groupDTO->name,
                'user_id' => null,
                'min_students_num' => $groupDTO->minStudentsNum,
                'max_students_num' => $groupDTO->maxStudentsNum,
                'min_skills_num' => $groupDTO->minSkillsNum,
                'max_skills_num' => $groupDTO->maxSkillsNum,
                'max_useless_skill_students' => $groupDTO->maxUselessSkillStudents,
                'enabled' => $groupDTO->enabled
            ]);
            $group->skills()->sync($skills);
            self::commit();
            return $group;
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            self::rollBack();
            return null;
        }
    }

    /**
     * @param Group  $group
     * @param array[skill_id] $skills
     *
     * @return bool
     */
    public static function change(GroupDTO $group, array $skills): bool
    {
        try {
            self::beginTransaction();
            $changedGroup = Group::where('id', $group->id)->first();
            $changedGroup->name = $group->name;
            $changedGroup->user_id = $group->user_id;
            $changedGroup->min_students_num = $group->min_students_num;
            $changedGroup->max_students_num = $group->max_students_num;
            $changedGroup->min_skills_num = $group->min_skills_num;
            $changedGroup->max_skills_num = $group->max_skills_num;
            $changedGroup->max_useless_skill_students = $group->max_useless_skill_students;
            $changedGroup->enabled = $group->enabled;
            $changedGroup->save();
            $changedGroup->skills()->sync($skills);
            // @TODO изменения группы приводит к возможному изменению студентов.
            self::commit();

            return true;
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            self::rollBack();
            return false;
        }
    }

    public static function remove(int $groupID): bool
    {
        try {
            $group = Group::find($groupID);
            if (empty($group)) {
                return false;
            }
            self::beginTransaction();
            $group->delete();
            self::commit();
            return true;
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            self::rollBack();
        }
    }
}
