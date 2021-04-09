<?php

namespace App\Repository;

use App\Models\Group;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Capsule\Manager as DB;
use League\Route\Http\Exception\BadRequestException;
use League\Route\Http\Exception\NotFoundException;

class StudentRepository extends Repository
{

    /**
     * @return Group
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function findSuitableGroup(User $user): Group
    {
        $skills = $user->skills()->get();

        if (empty($skills)) {
            throw new NotFoundException('skills not found');
        }

        $skillIds = $skills->pluck('id')->toArray();

        if (empty($skillIds)) {
            throw new BadRequestException('cannot get skill ids');
        }

        return $this->findGroup($skillIds);
    }

    /**
     * @param array $skillIds
     * @return Group
     * @throws NotFoundException
     */
    private function findGroup(array $skillIds): Group
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

    public function getStudentByID(int $studentID): Student
    {
        return Student::findByID($studentID);
    }

    public function getStudentByEmail(string $email): ?Student
    {
        return Student::findByEmail($email);
    }

    public function create(User $user, array $skillIDs): ?Student
    {
        return Student::insert($user, $skillIDs);
    }

    public function update(User $user, ?array $skillIDs): bool
    {
        return Student::change($user, $skillIDs);
    }

    public function delete(int $userID): bool
    {
        return Student::remove($userID);
    }

    public function getSkillIDsByStudentID(int $studentID): ?array
    {
        $skillIDs = $this->cache->getUserSkills($studentID);
        if (!empty($skillIDs)) {
            return $skillIDs;
        }
        $student = Student::findByID($studentID);
        $skillIDs = [];
        foreach ($student->skills as $item) {
            $skillIDs[] = $item;
        }
        if (empty($skillIDs)) {
            return null;
        }
        $this->cache->setUserSkills($studentID, $skillIDs);
        return $skillIDs;
    }

    /**
     * @param int $studentID
     *
     * @return array|null
     * @throws NotFoundException
     */
    public function getGroupIDsByStudentID(int $studentID): ?array
    {
        $student = Student::findByID($studentID);
        $groupsIDs = [];
        foreach ($student->user->groups as $item) {
            $groupsIDs[] = $item;
        }
        if (empty($groupsIDs)) {
            return null;
        }
        return $groupsIDs;
    }

    /**
     * @param int   $userID
     * @param array $skillIDs
     *
     * @return bool
     * @throws \App\Exceptions\StudentException
     */
    public function setSkills(int $userID, array $skillIDs): bool
    {
        $user = User::where('id', $userID)->first();
        if ($user) {
            $result = Student::change($user, $skillIDs);
        } else {
            $result = Student::insert($user, $skillIDs);
        }
        return !empty($result);
    }

    /**
     * @param User $user
     *
     * @return bool
     * @throws NotFoundException
     * @throws \App\Exceptions\StudentException
     */
    public function setUserData(User $user): bool
    {
        $student = $this->getStudentByID($user->id);
        if (empty($student)) {
            throw new NotFoundException(
                'Can not set data of Student. Student (' . $user->id . ') not found'
            );
        }

        return Student::change($user, null);
    }

    /**
     * @param int $userID
     * @param int $groupID
     *
     * @return bool
     * @throws NotFoundException
     */
    public function addGroup(int $userID, int $groupID): bool
    {
        $user = User::where('id', $userID)->first();
        if (empty($user)) {
            throw new NotFoundException(
                'Can not add Student (' . $userID . ') to Group (' . $groupID . '). Student not found'
            );
        }
        $result = $user->groups()->sync($groupID);
        if (empty($result)) {
            return false;
        }
        return true;
    }

    /**
     * @param int $userID
     * @param int $groupID
     *
     * @return bool
     * @throws NotFoundException
     */
    public function delGroup(int $userID, int $groupID): bool
    {
        $user = User::where('id', $userID)->first();
        if (empty($user)) {
            throw new NotFoundException(
                'Can not del Student (' . $userID . ') from Group (' . $groupID . '). Student not found'
            );
        }
        $result = $user->groups()->toggle($groupID);
        if (empty($result)) {
            return false;
        }
        return true;
    }
}
