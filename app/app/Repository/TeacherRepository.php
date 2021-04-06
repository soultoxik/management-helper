<?php


namespace App\Repository;


use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Capsule\Manager as DB;
use League\Route\Http\Exception\BadRequestException;
use League\Route\Http\Exception\NotFoundException;

class TeacherRepository
{
    private User $user;
    private Group $group;

    public function __construct(User $user)
    {
        if (!$user->teacher) {
            throw new BadRequestException('the user is not a teacher');
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

    /**
     * @param array $skillIds
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object
     * @throws NotFoundException
     */
    private function findGroup(array $skillIds)
    {
        $conditions = $this->user->teacherConditions()->first();
        if (empty($conditions)) {
            throw new NotFoundException('conditions not found');
        }

        $counter = DB::table('groups')
            ->select('groups.id', DB::raw('count(groups.id) as counter'))
            ->join('groups_skills', 'groups.id', '=', 'groups_skills.group_id')
            ->leftJoin('skills', 'groups_skills.skill_id', '=', 'skills.id')
            ->whereIn('skill_id', $skillIds)
            ->groupBy('groups.id')
            ->orderBy('counter', 'desc')
            ->orderBy('id')
            ->limit(100);

        /** @var Group $group */
        $group = Group::query()
            ->joinSub($counter, 'counter', function ($join) {
                $join->on('groups.id', '=', 'counter.id');
            })
            ->whereNull('user_id')
            ->whereIn('groups.id', function ($query) use ($conditions) {
                $query->select('id')
                    ->from('groups')
                    ->where('min_students_num', '>=', $conditions['min_group_size'])
                    ->where('max_students_num', '<=', $conditions['max_group_size']);
            });

        if (!empty($this->user->teacherGroups()->first())) {
            $group = $group->where(function ($query) {
                $query->select(DB::raw('count(id) as counter'))
                    ->from('groups')
                    ->where('user_id', $this->user->id)
                    ->groupBy('user_id');
            }, '<', $conditions['max_groups_num']);
        }

        $group = $group->first();

        if (empty($group)) {
            throw new NotFoundException('group not found for this user');
        }

        return $group;
    }

    public function addToGroup()
    {
        $this->group->user_id = $this->user->id;
        $this->group->save();
    }

    public function getGroup()
    {
        return $this->group;
    }
}