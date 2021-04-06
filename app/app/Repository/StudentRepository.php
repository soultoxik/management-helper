<?php

namespace App\Repository;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Capsule\Manager as DB;
use League\Route\Http\Exception\BadRequestException;
use League\Route\Http\Exception\NotFoundException;

class StudentRepository
{
    private User $user;
    private Group $group;

    public function __construct(User $user)
    {
        if ($user->teacher) {
            throw new BadRequestException('the user is not a student');
        }

        $this->user = $user;
    }

    /**
     * @return Group
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function findSuitableGroup()
    {
        $skills = $this->user->skills()->get();

        if (empty($skills)) {
            throw new NotFoundException('skills not found');
        }

        $skillIds = $skills->pluck('id')->toArray();

        if (empty($skillIds)) {
            throw new BadRequestException('cannot get skill ids');
        }

        $this->group = $this->findGroup($skillIds);

        return $this->group;
    }

    public function addToGroup()
    {
        $this->user->groups()->sync([$this->group->id]);
    }

    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param array $skillIds
     * @return Group
     * @throws NotFoundException
     */
    private function findGroup(array $skillIds)
    {
        $counter = DB::table('groups')
            ->select('groups.id', DB::raw('count(groups.id) as counter'))
            ->join('groups_skills', 'groups.id', '=', 'groups_skills.group_id')
            ->leftJoin('skills', 'groups_skills.skill_id', '=', 'skills.id')
            ->whereIn('skill_id', $skillIds)
            ->groupBy('groups.id')
            ->limit(100);

        /** @var Group $group */
        $group = Group::query()
            ->joinSub($counter, 'counter', function ($join) {
                $join->on('groups.id', '=', 'counter.id');
            })->where('max_students_num', '>', function ($query) {
                $query->select(DB::raw('count(groups_users.id) as counter'))
                    ->from('groups_users')
                    ->join('users', 'users.id', '=', 'groups_users.user_id')
                    ->where('teacher', );
            })->orderBy('counter', 'desc')
            ->first();

        if (empty($group)) {
            throw new NotFoundException('group not found for this user');
        }

        return $group;
    }
}
