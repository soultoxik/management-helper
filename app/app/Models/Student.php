<?php


namespace App\Models;

use App\Exceptions\StudentException;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Traits\Transaction;
use League\Route\Http\Exception\NotFoundException;

class Student extends Model
{
    use Transaction;

    public User $user;
    public ?Collection $skills;

    public function __construct(User $user, ?Collection $skills)
    {
        parent::__construct();
        $this->user = $user;
        $this->skills = $skills;
    }

    public static function findByID(int $id): Student
    {
        $user = User::where('id', $id)->where('teacher', false)->first();
        if (empty($user)) {
            throw new NotFoundException(
                'Student not found by ID:' . $id
            );
        }
        $skills = $user->skills;
        return new Student($user, $skills);
    }

    public static function findByEmail(string $email): ?Student
    {
        $user = User::where('email', $email)->where('teacher', false)->first();
        if (empty($user)) {
            throw new NotFoundException(
                'Student not found by email:' . $email
            );
        }
        $skills = $user->skills;
        return new Student($user, $skills);
    }

    /**
     * @param User $user
     * @param array[skill_id] $skills
     *
     * @return Student|null
     * @throws StudentException
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

        } catch (Exception $e) {
            self::rollBack();
            throw new StudentException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param User $user
     * @param array[skill_id] $skills
     *
     * @return bool
     * @throws StudentException
     */
    public static function change(User $user, ?array $skills): bool
    {
        try {
            self::beginTransaction();
            $newUser = User::where('id', $user->id)->first();
            if (empty($newUser)) {
                throw new NotFoundException(
                    'Can not change Student. Student (' . $user->id . ') not found'
                );
            }
            $newUser->email = $user->email;
            $newUser->first_name = $user->first_name;
            $newUser->last_name = $user->last_name;
            $newUser->phone = $user->phone;
            $newUser->enabled = $user->enabled;
            $newUser->teacher = $user->teacher;
            $newUser->save();
            if (!empty($skills)) {
                $newUser->skills()->sync($skills);
            }
            // @TODO изменения скиллов может привести к исключению студента из группы.
            self::commit();

            return true;
        } catch (Exception $e) {
            self::rollBack();
            throw new StudentException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param int $userID
     *
     * @return bool
     * @throws StudentException
     */
    public static function remove(int $userID): bool
    {
        try {
            $student = self::findByID($userID);
            if (empty($student)) {
                throw new NotFoundException(
                    'Can not remove Student. Student (' . $userID . ') not found'
                );
            }
            $student->user->delete();
            return true;
        } catch (Exception $e) {
            throw new StudentException($e->getMessage(), $e->getCode());
        }
    }
}