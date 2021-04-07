<?php


namespace App\Models;

use App\Actions\ActionsTeacherCondition;
use App\Exceptions\TeacherConditionException;
use App\Models\DTOs\TeacherConditionDTO;
use App\Storage\RedisDAO;
use Exception;
use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;
use Illuminate\Database\Eloquent\Model;

class TeacherCondition extends Model
{
    use PivotEventTrait;

    protected $table = 'teachers_conditions';
    protected $fillable = [
        'user_id',
        'max_groups_num',
        'min_group_size',
        'max_group_size',
    ];

    /**
     * @param TeacherConditionDTO $teacherConditionDTO
     *
     * @return TeacherCondition|null
     * @throws TeacherConditionException
     */
    public static function insert(TeacherConditionDTO $teacherConditionDTO): ?TeacherCondition
    {
        try {
            return TeacherCondition::create([
                'user_id' => $teacherConditionDTO->userID,
                'max_groups_num' => $teacherConditionDTO->maxGroupsNum,
                'min_group_size' => $teacherConditionDTO->minGroupSize,
                'max_group_size' => $teacherConditionDTO->maxGroupSize,
            ]);
        } catch (Exception $e) {
            throw new TeacherConditionException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param TeacherCondition $newTeacherCondition
     *
     * @return bool
     * @throws TeacherConditionException
     */
    public static function change(TeacherCondition $newTeacherCondition): bool
    {
        try {
            $teacherCondition = TeacherCondition::where('id', $newTeacherCondition->id)->first();
            $teacherCondition->user_id = $newTeacherCondition->user_id;
            $teacherCondition->max_groups_num = $newTeacherCondition->max_groups_num;
            $teacherCondition->min_group_size = $newTeacherCondition->min_group_size;
            $teacherCondition->max_group_size = $newTeacherCondition->max_group_size;
            return $teacherCondition->save();
            // @TODO изменения группы приводит к возможному изменению студентов.
        } catch (Exception $e) {
            throw new TeacherConditionException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param int int
     *
     * @return bool
     * @throws TeacherConditionException
     */
    public static function remove(int $id): bool
    {
        try {
            $teacherCondition = TeacherCondition::where('id', $id)->first();
            if (empty($teacherCondition)) {
                return false;
            }
            $teacherCondition->delete();
            return true;
        } catch (Exception $e) {
            throw new TeacherConditionException($e->getMessage(), $e->getCode());
        }
    }

    public static function boot()
    {
        parent::boot();
        $action = new ActionsTeacherCondition();
        static::deleted(function ($model) use ($action) {
            $action->delete($model);
        });
        static::saved(function ($model) use ($action) {
            $action->save($model);
        });
    }
}