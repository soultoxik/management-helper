<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use Transaction;

    public User $user;

    /**
     *  var array Skill
    */
    public ?array $skills;

    public function __construct(User $user, ?array $skills)
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
        $data = $user->skills;
        if (empty($data)) {
            return new Student($user, null);
        }
        $skills = [];
        foreach ($data as $item) {
            $skills[] = $item;
        }
        if (empty($skills)) {
            $skills = null;
        }
        return new Student($user, $skills);
    }

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
            self::commit();
            $data = [];
            foreach ($skills as $skillID) {
                $skill = Skill::where('id', $skillID)->first();
                if ($skill) {
                    $data[] = $skill;
                }
            }

            return new Student($user, $data);

        } catch (\Exception $e) {
            var_dump($e->getMessage());
            self::rollBack();
            return null;
        }
    }

    public static function change(User $user, array $skills): bool
    {
        try {
            self::beginTransaction();
            $user->update([
                'email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'phone' =>  $user->phone,
                'enabled' => $user->enabled,
                'teacher' => $user->teacher
            ]);
            // @TODO изменения скиллов может привести к исключению студента из группы.
            $user->skills()->sync($skills);
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