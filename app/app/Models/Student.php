<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class Student extends Model
{
    use Transaction;

    public User $user;
    public ?Collection $skills;

    public function __construct(User $user, ?Collection $skills)
    {
        $this->user = $user;
        $this->skills = $skills;
    }

    public static function findByID(int $id): ?Student
    {
        $user = User::where('id', $id)->where('teacher', false)->first();
        if (empty($user)) {
            return null;
        }
        $skills = $user->skills;
        return new Student($user, $skills);
    }

    public static function findByEmail(string $email): ?Student
    {
        $user = User::where('email', $email)->where('teacher', false)->first();
        if (empty($user)) {
            return null;
        }
        $skills = $user->skills;
        return new Student($user, $skills);
    }

    /**
     * @param User  $user
     * @param array[skill_id] $skills
     *
     * @return Student|null
     */
    public static function insert(User $user, array $skills): ?Student
    {
        try {
            self::beginTransaction();

            $user = User::create([
                'email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'phone' =>  $user->phone,
                'enabled' => true,
                'teacher' => false
            ]);
            $user->skills()->sync($skills);
            $skills = $user->skills()->get();
            self::commit();

            return new Student($user, $skills);

        } catch (\Exception $e) {
            var_dump($e->getMessage());
            self::rollBack();
            return null;
        }
    }

    /**
     * @param User  $user
     * @param array[skill_id] $skills
     *
     * @return bool
     */
    public static function change(User $user, array $skills): bool
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
            // @TODO изменения скиллов может привести к исключению студента из группы.
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
            $student = self::findByID($userID);
            if (empty($student)) {
                return false;
            }
            self::beginTransaction();
            $student->user->delete();
            self::commit();
            return true;
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            self::rollBack();
        }
    }


}