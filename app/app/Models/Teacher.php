<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use App\Models\DTOs\TeacherConditionDTO;

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

    public static function findByID(int $id): ?Teacher
    {
        $user = User::where('id', $id)->where('teacher', true)->first();
        if (empty($user)) {
            return null;
        }
        $skills = $user->skills;
        $condition = $user->teacherConditions->first();
        return new Teacher($user, $skills, $condition);
    }

    public static function findByEmail(string $email): ?Teacher
    {
        $user = User::where('email', $email)->where('teacher', true)->first();
        if (empty($user)) {
            return null;
        }
        $skills = $user->skills;
        $condition = $user->teacherConditions->first();
        return new Teacher($user, $skills, $condition);
    }

    /**
     * @param User  $user
     * @param array[skill_id] $skills
     * @param TeacherConditionDTO $condition
     *
     * @return Teacher|null
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
                'teacher' => true
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
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            self::rollBack();
            return null;
        }
    }

    /**
     * @param User  $user
     * @param array[skill_id] $skills
     * @param TeacherCondition $condition
     *
     * @return bool
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
            // @TODO изменения условий студента приводит к изменению группы.
            self::commit();

            return true;
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            self::rollBack();
            return false;
        }
    }

    public static function remove(int $userID): bool
    {
        try {
            $teacher = self::findByID($userID);
            if (empty($teacher)) {
                return false;
            }
            self::beginTransaction();
            $teacher->user->delete();
            self::commit();
            return true;
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            self::rollBack();
        }
    }
}
