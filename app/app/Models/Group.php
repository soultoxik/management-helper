<?php


namespace App\Models;

use App\Exceptions\GroupException;
use App\Models\Traits\Transaction;
use App\Storage\RedisDAO;
use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;
use Illuminate\Database\Eloquent\Model;
use App\Models\DTOs\GroupDTO;
use Illuminate\Support\Collection;
use Exception;

class Group extends Model
{
    use Transaction;
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
     * @param array[skill_id] $skills
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
     * @param array[skill_id] $skills
     *
     * @return bool
     * @throws GroupException
     */
    public static function change(Group $group): bool
    {
        try {
            $changedGroup = Group::where('id', $group->id)->first();
            $changedGroup->name = $group->name;
            $changedGroup->min_students_num = $group->min_students_num;
            $changedGroup->max_students_num = $group->max_students_num;
            $changedGroup->min_skills_num = $group->min_skills_num;
            $changedGroup->max_skills_num = $group->max_skills_num;
            $changedGroup->max_useless_skill_students = $group->max_useless_skill_students;
            $changedGroup->enabled = $group->enabled;
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
     */
    public static function remove(int $groupID): bool
    {
        try {
            $group = Group::where('id', $groupID)->first();
            if (empty($group)) {
                return false;
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

        $redis = new RedisDAO();
        static::pivotAttached(function ($model, $relationName, $pivotIds, $pivotIdsAttributes) use ($redis) {
            if ($relationName == 'skills') {
                foreach ($pivotIds as $skillID) {
                    $redis->addSkillToGroup($model->id, $skillID);
                }
            }
            if ($relationName == 'students') {
                foreach ($pivotIds as $skillID) {
                    $redis->addUserToGroup($model->id, $skillID);
                }
            }
        });
        static::pivotDetached(function ($model, $relationName, $pivotIds) use ($redis) {
            if ($relationName == 'skills') {
                foreach ($pivotIds as $skillID) {
                    $redis->delSkillFromGroup($model->id, $skillID);
                }
            }
            if ($relationName == 'students') {
                foreach ($pivotIds as $skillID) {
                    $redis->delUserFromGroup($model->id, $skillID);
                }
            }
        });

        static::deleted(function ($model) use ($redis) {
            $redis->delGroup($model->id);
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


//    public static function boot()
//    {
//        parent::boot();
//        $redis = new RedisDAO();
////        static::creating(function($item) {
////            var_dump('creating');
////        });
////        static::created(function ($item) {
////            $redis = new RedisDAO();
////            $redis->setGroup($item);
////        });
////        static::updating(function($item) {
////            var_dump('updating');
////        });
////        static::updated(function ($item) use ($redis) {
////            var_dump('updated');
////            $redis->setGroup($item);
////        });
////        static::saving(function($item) {
////            var_dump('saving');
////        });
//        static::saved(function ($item) use ($redis) {
//            var_dump('Group saved');
////            $redis->setGroup($item);
//        });
////        static::updated(function ($item) use ($redis) {
////            $redis->setGroup($item);
////        });
////        static::inserted(function ($model, $group, $skills) use ($redis) {
////            $redis->setGroup($group);
////            $redis->setGroupSkills($group->id, $skills);
////        });
////        static::changed(function ($model, $group, $skills) use ($redis) {
////            $redis->setGroup($group);
////            $redis->setGroupSkills($group->id, $skills);
////        });
////        static::removed(function ($model, $group) use ($redis) {
////            $redis->delGroup($group->id);
////            $redis->delGroupSkills($group->id);
////        });
//    }

//    public function fireModelEvent($event, $halt = true, ?Group $group = null, array $skills = null)
//    {
//        if (!isset(static::$dispatcher)) {
//            return true;
//        }
//
//        // First, we will get the proper method to call on the event dispatcher, and then we
//        // will attempt to fire a custom, object based event for the given event. If that
//        // returns a result we can return that result, or we'll call the string events.
//        $method = $halt ? 'until' : 'dispatch';
//
//        $result = $this->filterModelEventResults(
//            $this->fireCustomModelEvent($event, $method)
//        );
//
//        if (false === $result) {
//            return false;
//        }
//
//        $payload = [$this, $group, $skills];
//
//        return !empty($result) ? $result : static::$dispatcher->{$method}(
//            "eloquent.{$event}: ".static::class, $payload
//        );
//    }

}

