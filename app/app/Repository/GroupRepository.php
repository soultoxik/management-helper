<?php


namespace App\Repository;

use App\Exceptions\GroupRepositoryException;
use App\Models\DTOs\GroupDTO;
use App\Models\Group;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Capsule\Manager as DB;
use League\Route\Http\Exception\BadRequestException;
use League\Route\Http\Exception\NotFoundException;
use App\Repository\Traits\CacheTrait;
use Exception;

class GroupRepository
{
    use CacheTrait;

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

    public function getGroup(int $groupID): ?Group
    {
        $group = $this->cache->getGroup($groupID);
        if (!empty($group)) {
            $group->id = $groupID;
            return $group;
        }
        $group = Group::where('id', $groupID)->first();
        if (empty($group)) {
            return null;
        }
        $this->cache->setGroup($group);
        return $group;
    }

    public function getGroupFull(int $groupID): ?Group
    {
        $group = $this->getGroup($groupID);
        if (empty($groupID)) {
            return null;
        }
        $group->setStudents($group->students()->get());
        $group->setSkills($group->skills()->get());
        return $group;
    }

    public function create(GroupDTO $groupDTO): ?Group
    {
        $group = Group::insert($groupDTO);
        if (!empty($group)) {
            $this->cache->setGroup($group);
        }
        return $group;
    }

    public function update(Group $newGroup): bool
    {
        $result = Group::change($newGroup);
        if ($result) {
            $this->cache->setGroup($newGroup);
        }
        return $result;
    }

    public function delete(int $groupID): bool
    {
        return Group::remove($groupID);
    }

    public function getSkillIDsByGroupID(int $groupID): ?array
    {
        $skills = $this->cache->getGroupSkills($groupID);
        if (!empty($skills)) {
            return $skills;
        }
        $group = Group::where('id', $groupID)->first();
        $skills = $group->skills;
        $skillIDs = [];
        foreach ($skills as $item) {
            $skillIDs[] = $item;
        }
        if (empty($skillIDs)) {
            return null;
        }
        $this->cache->setGroupSkills($groupID, $skillIDs);
        return $skillIDs;
    }

    public function getStudentIDsByGroupID(int $groupID): ?array
    {
        $students = $this->cache->getGroupUsers($groupID);
        if (!empty($students)) {
            return $students;
        }
        $group = Group::where('id', $groupID)->first();
        $students = $group->students;
        $studentIDs = [];
        foreach ($students as $item) {
            $studentIDs[] = $item->id;
        }
        if (empty($studentIDs)) {
            return null;
        }
        $this->cache->setGroupUsers($groupID, $studentIDs);
        return $studentIDs;
    }

    public function getTeacherIDByGroupID(int $groupID): ?int
    {
        $group = $this->cache->getGroup($groupID);
        if (isset($group->user_id)) {
            return $group->user_id;
        }
        $group = Group::where('id', $groupID)->first();
        if (empty($group)) {
            return null;
        }
        return $group->user_id;
    }

    /**
     * @param int   $groupID
     * @param array $skillIDs
     *
     * @return bool
     * @throws GroupRepositoryException
     */
    public function setSkillsByGroupID(int $groupID, array $skillIDs): bool
    {
        try {
            $group = Group::where('id', $groupID)->first();
            if (empty($group)) {
                return false;
            }

            $result = $group->skills()->sync($skillIDs);
        } catch (Exception $e) {
            throw new GroupRepositoryException($e->getMessage(), $e->getCode());
        }
        if (empty($result)) {
            return false;
        }

        return true;
    }

    /**
     * @param int   $groupID
     * @param array $studentIDs
     *
     * @return bool
     * @throws GroupRepositoryException
     */
    public function setStudentsByGroupID(int $groupID, array $studentIDs): bool
    {
        try {
            $group = Group::where('id', $groupID)->first();
            if (empty($group)) {
                return false;
            }
            $result = $group->students()->sync($studentIDs);
        } catch (Exception $e) {
            throw new GroupRepositoryException($e->getMessage(), $e->getCode());
        }

        if (empty($result)) {
            return false;
        }
        return true;
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