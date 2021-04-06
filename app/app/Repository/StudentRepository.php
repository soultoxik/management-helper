<?php

namespace App\Repository;

use App\Models\Student;
use App\Models\User;
use App\Repository\Traits\CacheTrait;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use League\Route\Http\Exception\BadRequestException;
use League\Route\Http\Exception\NotFoundException;

class StudentRepository
{
    use CacheTrait;

    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return Model|Builder|object|null
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

        $counter = DB::table('groups')
            ->select('groups.id', DB::raw('count(groups.id) as counter'))
            ->join('groups_skills', 'groups.id', '=', 'groups_skills.group_id')
            ->leftJoin('skills', 'groups_skills.skill_id', '=', 'skills.id')
            ->whereIn('skill_id', $skillIds)
            ->groupBy('groups.id')
            ->orderBy('counter', 'desc')
            ->limit(1);

        return DB::table('groups')
            ->joinSub($counter,'counter', function ($join) {
                $join->on('groups.id', '=', 'counter.id');
            })->first();
    }

    public function getSkillIDsByStudentID(int $studentID): ?array
    {
        $skillIDs = $this->cache->getUserSkills($studentID);
        if (!empty($skillIDs)) {
            return $skillIDs;
        }
        $student = Student::findByID($studentID);
        if (empty($student)) {
            return null;
        }
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

    public function getGroupIDsByStudentID(int $studentID): ?array
    {
        $student = Student::findByID($studentID);
        if (empty($student)) {
            return null;
        }
        $groupsIDs = [];
        foreach ($student->user->groups as $item) {
            $groupsIDs[] = $item;
        }
        if (empty($groupsIDs)) {
            return null;
        }
        return $groupsIDs;
    }

    public function getStudentByID(int $studentID): ?Student
    {
        return Student::findByID($studentID);
    }

    public function getStudentByEmail(string $email): ?Student
    {
        return Student::findByEmail($email);
    }

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

    public function setUserData(User $user): bool
    {
        $student = $this->getStudentByID($user->id);
        if (empty($student)) {
            return false;
        }

        return Student::change($user, null);
    }
}
