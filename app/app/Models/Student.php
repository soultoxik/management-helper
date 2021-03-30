<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected User $user;
    protected ?Collection $skill;

    public function __construct(User $user, Collection $skill)
    {
        $this->user = $user;
        $this->skill = $skill;
    }

    public static function findByID(int $id)
    {
        $user = User::where('id', $id)->where('teacher', false)->first();
        $skill = $user->skills;
        if (empty($skill)) {
            return new Student($user, null);
        }
        return new Student($user, $skill);
    }

    public static function create(User $user, array $skills)
    {
        $user = User::create([
            'email' => $user->email,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'phone' =>  $user->phone,
            'enabled' => true,
            'teacher' => false
        ]);
        // не работает
        $user->skills()->sync($skills);
    }
}