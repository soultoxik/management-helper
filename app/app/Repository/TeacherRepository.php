<?php


namespace App\Repository;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Capsule\Manager as DB;
use League\Route\Http\Exception\BadRequestException;
use League\Route\Http\Exception\NotFoundException;
use App\Models\DTOs\TeacherConditionDTO;
use App\Models\Teacher;
use App\Models\TeacherCondition;

class TeacherRepository extends Repository
{
    private User $user;
    private Group $group;

    public function getTeacherByID(int $teacherID): Teacher
    {
        return Teacher::findByID($teacherID);
    }

    public function getTeacherByEmail(string $email): ?Teacher
    {
        return Teacher::findByEmail($email);
    }

    public function create(
        User $user,
        array $skills,
        TeacherConditionDTO $conditionDTO
    ): ?Teacher
    {
        return Teacher::insert($user, $skills, $conditionDTO);
    }

    public function update(
        User $user,
        array $skills,
        TeacherCondition $condition
    ): bool
    {
        return Teacher::change($user, $skills, $condition);
    }

    public function delete(int $userID): bool
    {
        return Teacher::remove($userID);
    }

    /**
     * @return Group
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function findSuitableGroup(Teacher $teacher): Group
    {
        $skills = $teacher->user->skills()->get();

        if (empty($skills)) {
            throw new NotFoundException('skills not found');
        }

        $skillIds = $skills->pluck('id')->toArray();

        if (empty($skillIds)) {
            throw new BadRequestException('cannot get skill ids');
        }
        return $this->findGroup($skillIds, $teacher->user);
    }

    /**
     * @param array $skillIds
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object
     * @throws NotFoundException
     */
    private function findGroup(array $skillIds, $user)
    {
        if (empty($user->teacherConditions()->first())) {
            throw new NotFoundException('conditions not found');
        }

        $conditions = $user->teacherConditions()->first()->toArray();

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
            })
            ->whereNull('user_id')
            ->whereIn('groups.id', function ($query) use ($conditions) {
                $query->select('id')
                    ->from('groups')
                    ->where('min_students_num', '>=', $conditions['min_group_size'])
                    ->where('max_students_num', '<=', $conditions['max_group_size']);
            });

        if (!empty($user->teacherGroups()->first())) {
            $group = $group->where(function ($query) {
                $query->select(DB::raw('count(id) as counter'))
                    ->from('groups')
                    ->where('user_id', $this->user->id)
                    ->groupBy('user_id');
            }, '<', $conditions['max_groups_num']);
        }

        $group = $group
            ->orderBy('counter', 'desc')
            ->orderBy('groups.id')
            ->first();

        if (empty($group)) {
            throw new NotFoundException('group not found for this Teacher');
        }

        return $group;
    }

    public function addToGroup(Group $group, Teacher $teacher): bool
    {
        $group->user_id = $teacher->user->id;
        return $group->save();
    }
//
//    public function getGroup(): Group
//    {
//        return $this->group;
//    }
}