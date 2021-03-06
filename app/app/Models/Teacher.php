<?php


namespace App\Models;

use App\Exceptions\TeacherException;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use App\Models\DTOs\TeacherConditionDTO;
use App\Models\Traits\Transaction;
use League\Route\Http\Exception\NotFoundException;

class Teacher extends Model
{
    const IS_A_TEACHER = true;
    const IS_NOT_A_TEACHER = false;

    use Transaction;

    public User $user;
    public ?Collection $skills;
    public ?TeacherCondition $teacherCondition;

    public function __construct(
        User $user,
        ?Collection $skills,
        ?TeacherCondition $teacherCondition
    ) {
        parent::__construct();
        $this->user = $user;
        $this->skills = $skills;
        $this->teacherCondition = $teacherCondition;
    }

    public static function findByID(int $id): Teacher
    {
        $user = User::where('id', $id)->where('teacher', self::IS_A_TEACHER)->first();
        if (empty($user)) {
            throw new NotFoundException('Teacher (' . $id . ') not found');
        }
        $skills = $user->skills;
        $condition = $user->teacherConditions;
        return new Teacher($user, $skills, $condition);
    }

    public static function findByEmail(string $email): ?Teacher
    {
        $user = User::where('email', $email)->where('teacher', self::IS_A_TEACHER)->first();
        if (empty($user)) {
            throw new NotFoundException('Teacher not found, by email:' . $email);
        }
        $skills = $user->skills;
        $condition = $user->teacherConditions->first();
        return new Teacher($user, $skills, $condition);
    }

    /**
     * @param User            $user
     * @param array[skill_id] $skills
     * @param TeacherConditionDTO $condition
     *
     * @return Teacher|null
     * @throws TeacherException
     */
    public static function insert(
        User $user,
        array $skills,
        TeacherConditionDTO $condition
    ): ?Teacher
    {
        try {
            self::beginTransaction();

            $user = User::create([
                'email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'phone' =>  $user->phone,
                'enabled' => true,
                'teacher' => self::IS_A_TEACHER
            ]);
            $user->skills()->sync($skills);
            $skills = $user->skills()->get();
            $condition = TeacherCondition::create([
                'user_id' => $user->id,
                'max_groups_num' => $condition->maxGroupsNum,
                'min_group_size' => $condition->minGroupSize,
                'max_group_size' => $condition->maxGroupSize,
            ]);
            self::commit();
            return new Teacher($user, $skills, $condition);
        } catch (Exception $e) {
            self::rollBack();
            throw new TeacherException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param User             $user
     * @param array[skill_id] $skills
     * @param TeacherCondition $condition
     *
     * @return bool
     * @throws TeacherException
     */
    public static function change(
        User $user,
        array $skills,
        TeacherCondition $condition
    ): bool
    {
        try {
            self::beginTransaction();
            $newUser = User::where('id', $user->id)->first();
            $newUser->email = $user->email;
            $newUser->first_name = $user->first_name;
            $newUser->last_name = $user->last_name;
            $newUser->phone = $user->phone;
            $newUser->enabled = $user->enabled;
            $newUser->teacher = $user->teacher;
            $newUser->save();
            $newUser->skills()->sync($skills);
            $newTeacherCondition = TeacherCondition::where('user_id', $user->id)->first();
            $newTeacherCondition->max_groups_num = $condition->max_groups_num;
            $newTeacherCondition->min_group_size = $condition->min_group_size;
            $newTeacherCondition->max_group_size = $condition->max_group_size;
            $newTeacherCondition->save();
            // @TODO ?????????????????? ?????????????? ???????????????? ???????????????? ?? ?????????????????? ????????????.
            self::commit();

            return true;
        } catch (Exception $e) {
            self::rollBack();
            throw new TeacherException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param int $userID
     *
     * @return bool
     * @throws TeacherException
     */
    public static function remove(int $userID): bool
    {
        try {
            $teacher = self::findByID($userID);
            if (empty($teacher)) {
                throw new NotFoundException(
                    'Can not remove Teacher. Teacher (' . $userID . ') not found'
                );
            }
            self::beginTransaction();
            $teacher->user->delete();
            $teacher->teacherCondition->delete();
            self::commit();
            return true;
        } catch (Exception $e) {
            self::rollBack();
            throw new TeacherException($e->getMessage(), $e->getCode());
        }
    }
}
