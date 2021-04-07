<?php


namespace App\Repository;


use App\Models\Group;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Capsule\Manager as DB;
use League\Route\Http\Exception\BadRequestException;
use League\Route\Http\Exception\NotFoundException;

class GroupRepository
{
    private Group $group;
    private User $user;

    public function findById(int $id)
    {
        $group = Group::find($id);

        if (empty($group)) {
            throw new NotFoundException('user not found');
        }

        $this->group = $group;

        return $this->group;
    }

    public function findSuitableTeacher()
    {
        $skills = $this->group->skills()->get();

        if (empty($skills)) {
            throw new NotFoundException('skills not found');
        }

        $skillIds = $skills->pluck('id')->toArray();

        if (empty($skillIds)) {
            throw new BadRequestException('cannot get skill ids');
        }

        $this->user = $this->findTeacher($skillIds);

        return $this->user;
    }

    private function findTeacher(array $skillIds)
    {
        $counter = DB::table('users')
            ->select('users.id', DB::raw('count(users.id) as counter'))
            ->join('users_skills', 'users.id', '=', 'users_skills.user_id')
            ->where('teacher', Teacher::IS_A_TEACHER)
            ->whereIn('users_skills.skill_id', $skillIds)
            ->groupBy('users.id')
            ->limit(100);

        /** @var User $user */
        $user = User::query()
            ->joinSub($counter, 'counter', function ($join) {
                $join->on('users.id', '=', 'counter.id');
            })
            ->orderBy('counter', 'desc')
            ->orderBy('users.id')->first();

        if (empty($user)) {
            throw new NotFoundException('group not found for this user');
        }

        return $user;
    }

    public function changeTo(int $teacherId)
    {
        $user = (new UserRepository())->findById($teacherId);
        if (!$user->teacher) {
            throw new BadRequestException('user is not a teacher');
        }

        $this->group->user_id = $user->id;
        $this->group->save();

        return $this->group;
    }

    public function addToGroup()
    {
        $this->group->user_id = $this->user->id;
        $this->group->save();
    }

    public function getTeacher()
    {
        return $this->user;
    }
}