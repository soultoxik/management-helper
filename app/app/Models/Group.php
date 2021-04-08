<?php


namespace App\Models;

use App\Actions\ActionsGroup;
use App\Exceptions\GroupException;
use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;
use Illuminate\Database\Eloquent\Model;
use App\Models\DTOs\GroupDTO;
use Illuminate\Support\Collection;
use Exception;
use League\Route\Http\Exception\NotFoundException;

class Group extends Model
{
    use PivotEventTrait;

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
    private ?Collection $skills;
    private ?Collection $students;

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
     * @param GroupDTO $group
     *
     * @return Group|null
     * @throws GroupException
     */
    public static function insert(GroupDTO $groupDTO): ?Group
    {
        try {
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
            return $group;
        } catch (Exception $e) {
            throw new GroupException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param Group $group
     *
     * @return bool
     * @throws GroupException
     * @throws NotFoundException
     */
    public static function change(Group $group): bool
    {
        try {
            $changedGroup = Group::where('id', $group->id)->first();
            if (empty($changedGroup)) {
                throw new NotFoundException(
                    'Can not update the Group. Group (' . $group->id . ') not found'
                );
            }

            $changedGroup->name = $group->name;
            $changedGroup->user_id = $group->user_id;
            $changedGroup->min_students_num = $group->min_students_num;
            $changedGroup->max_students_num = $group->max_students_num;
            $changedGroup->min_skills_num = $group->min_skills_num;
            $changedGroup->max_skills_num = $group->max_skills_num;
            $changedGroup->max_useless_skill_students = $group->max_useless_skill_students;
            $changedGroup->enabled = (bool) $group->enabled;
            $changedGroup->save();
            // @TODO изменения группы приводит к возможному изменению студентов.
            return true;
        } catch (Exception $e) {
            throw new GroupException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param int int
     *
     * @return bool
     * @throws GroupException
     * @throws NotFoundException
     */
    public static function remove(int $groupID): bool
    {
        try {
            $group = Group::where('id', $groupID)->first();
            if (empty($group)) {
                throw new NotFoundException(
                    'Can not remove the Group. Group (' . $groupID . ') not found'
                );
            }
            $group->delete();
            return true;
        } catch (Exception $e) {
            throw new GroupException($e->getMessage(), $e->getCode());
        }
    }

    public static function boot()
    {
        parent::boot();

        $actions = new ActionsGroup();
        static::pivotAttached(function ($model, $relationName, $pivotIds, $pivotIdsAttributes) use ($actions) {
            if ($relationName == 'skills') {
                $actions->addSkills($model->id, $pivotIds);
            }
            if ($relationName == 'students') {
                $actions->addStudents($model->id, $pivotIds);
            }
        });
        static::pivotDetached(function ($model, $relationName, $pivotIds) use ($actions) {
            if ($relationName == 'skills') {
                $actions->delSkills($model->id, $pivotIds);
            }
            if ($relationName == 'students') {
                $actions->delStudents($model->id, $pivotIds);
            }
        });
        static::deleted(function ($model) use ($actions) {
            $actions->delGroup($model->id);
        });
    }

    public function getSkills(): ?Collection
    {
        return $this->skills;
    }

    public function setSkills(?Collection $skills): void
    {
        $this->skills = $skills;
    }

    public function getStudents(): ?Collection
    {
        return $this->students;
    }

    public function setStudents(?Collection $students): void
    {
        $this->students = $students;
    }

}

