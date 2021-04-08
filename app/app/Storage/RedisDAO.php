<?php


namespace App\Storage;

use App\Exceptions\RedisDAOException;
use App\Models\Group;
use App\Models\TeacherCondition;
use Illuminate\Database\Eloquent\Model;
use \Redis;

/**
 *   Redis has struct:
 *     groups:$groupID - this is HASH, stores all properties from Group.
 *     group:skills:$groupID - this is Set, stores skills ID for Group.
 *     groups:users:$groupID - this is Set stores Users ID for Group.
 *     teacher:condition:$ID - this is HASH, stores all properties from TeacherCondition.
 *     teacher:condition:user_id:$userID - this is string, stores ID for
 *        TeacherCondition. This is helper, for function getTeacherConditionByUserID().
 *     user:skills:$userID - this Set, stores skills ID for User.
 */


class RedisDAO implements Cache
{

    private Redis $redis;

    public function __construct()
    {
        $this->redis = new Redis();
        $this->redis->connect('redis');
    }

    public function getGroup(int $groupID): ?Group
    {
        $key = $this->generateKeyGroup($groupID);
        $result = $this->redis->hGetAll($key);
        if (empty($result)) {
            return null;
        }
        return new Group($result);
    }

    public function setGroup(Group $group): bool
    {
        $key = $this->generateKeyGroup($group->id);
        return $this->setHash($key, $group);
    }

    public function delGroup(int $groupID): bool
    {
        $key = $this->generateKeyGroup($groupID);
        if (!$this->redis->del($key)) {
            return false;
        }
        $result = $this->delGroupSkills($groupID);
        if (!$result) {
            return false;
        }
        $result = $this->delGroupUsers($groupID);
        if (!$result) {
            return false;
        }
        return true;
    }

    private function generateKeyGroup(int $id): string
    {
        return 'groups:' . $id;
    }

    public function setGroupSkills(int $groupID, array $skillIDs): bool
    {
        $key = $this->generateKeyGroupSkills($groupID);
        $result = $this->updateSet($key, $skillIDs);
        if (empty($result)) {
            return false;
        }
        return in_array(0, $result);
    }

    public function addSkillToGroup(int $groupID, int $skillID): bool
    {
        $existSkillIDs = $this->getGroupSkills($groupID);
        $key = $this->generateKeyGroupSkills($groupID);
        if (empty($existSkillIDs)) {
            return $this->redis->sAdd($key, $skillID);
        }

        foreach ($existSkillIDs as $item) {
            if ($item == $skillID) {
                return true;
            }
        }
        return $this->redis->sAdd($key, $skillID);
    }

    public function delSkillFromGroup(int $groupID, int $skillID): bool
    {
        $existSkillIDs = $this->getGroupSkills($groupID);
        $key = $this->generateKeyGroupSkills($groupID);
        if (empty($existSkillIDs)) {
            return true;
        }

        foreach ($existSkillIDs as $item) {
            if ($item == $skillID) {
                return $this->redis->sRem($key, $skillID);
            }
        }
    }

    public function getGroupSkills(int $groupID): ?array
    {
        $key = $this->generateKeyGroupSkills($groupID);
        return $this->getSet($key);
    }

    public function delGroupSkills(int $groupID): bool
    {
        $key = $this->generateKeyGroupSkills($groupID);
        return $this->redis->del($key);
    }

    private function generateKeyGroupSkills(int $groupID): string
    {
        return 'group:skills:' . $groupID;
    }

    public function setGroupUsers(int $groupID, array $userIDs): bool
    {
        $key = $this->generateKeyGroupUsers($groupID);
        $result = $this->updateSet($key, $userIDs);
        if (empty($result)) {
            return false;
        }
        return in_array(0, $result);
    }

    public function getGroupUsers(int $groupID): ?array
    {
        $key = $this->generateKeyGroupUsers($groupID);
        return $this->getSet($key);
    }

    public function addUserToGroup(int $groupID, int $userID): bool
    {
        $key = $this->generateKeyGroupUsers($groupID);
        $existUserIDs = $this->getGroupUsers($groupID);
        if (empty($existUserIDs)) {
            return $this->redis->sAdd($key, $userID);
        }

        foreach ($existUserIDs as $item) {
            if ($item == $userID) {
                return true;
            }
        }
        return $this->redis->sAdd($key, $userID);
    }

    public function delUserFromGroup(int $groupID, int $skillID): bool
    {
        $existUserIDs = $this->getGroupUsers($groupID);
        $key = $this->generateKeyGroupUsers($groupID);
        if (empty($existUserIDs)) {
            return true;
        }

        foreach ($existUserIDs as $item) {
            if ($item == $skillID) {
                return $this->redis->sRem($key, $skillID);
            }
        }
    }

    public function delGroupUsers(int $groupID): bool
    {
        $key = $this->generateKeyGroupUsers($groupID);
        return $this->redis->del($key);
    }

    private function generateKeyGroupUsers(int $groupID): string
    {
        return 'group:users:' . $groupID;
    }

    public function setTeacherCondition(TeacherCondition $teacherCondition): bool
    {
        $key = $this->generateTeacherCondition($teacherCondition->id);
        $resultHash = $this->setHash($key, $teacherCondition);
        if (empty($resultHash)) {
            return false;
        }
        $key = $this->generateTeacherCondition('user_id:' . $teacherCondition->user_id);
        return $this->redis->set($key, $teacherCondition->id);
    }

    public function getTeacherConditionByID(int $id): ?TeacherCondition
    {
        $key = $this->generateTeacherCondition($id);
        $result = $this->redis->hGetAll($key);
        if (empty($result)) {
            return null;
        }
        return new TeacherCondition($result);
    }

    public function getTeacherConditionByUserID(int $userID): ?TeacherCondition
    {
        $key = $this->generateTeacherCondition('user_id:' . $userID);
        $teacherConditionID = $this->redis->get($key);
        if (empty($teacherConditionID)) {
            return null;
        }
        $key = $this->generateTeacherCondition($teacherConditionID);
        $result = $this->redis->hGetAll($key);
        if (empty($result)) {
            return null;
        }
        return new TeacherCondition($result);
    }

    public function delTeacherConditionByID(int $id): bool
    {
        $result = $this->getTeacherConditionByID($id);
        $key = $this->generateTeacherCondition($id);
        $deleted = $this->redis->del($key);
        $deletedHelper = true;
        if (!empty($result->user_id) && $deleted) {
            $key = $this->generateTeacherCondition('user_id:' . $result->user_id);
            $deletedHelper = $this->redis->del($key);
        }
        return $deleted && $deletedHelper;
    }

    private function generateTeacherCondition(string $id): string
    {
        return 'teacher:condition:' . $id;
    }

    public function setUserSkills(int $userID, array $skillIDs): bool
    {
        $key = $this->generateUserSkills($userID);
        $result = $this->updateSet($key, $skillIDs);
        if (empty($result)) {
            return false;
        }
        return in_array(0, $result);
    }

    public function getUserSkills(int $userID): ?array
    {
        $key = $this->generateUserSkills($userID);
        return $this->getSet($key);
    }

    public function addSkillToUser(int $userID, int $skillID): bool
    {
        $key = $this->generateUserSkills($userID);
        $existSkillIDs = $this->getUserSkills($userID);
        if (empty($existSkillIDs)) {
            return $this->redis->sAdd($key, $skillID);
        }

        foreach ($existSkillIDs as $item) {
            if ($item == $skillID) {
                return true;
            }
        }
        return $this->redis->sAdd($key, $skillID);
    }

    public function delSkillFromUser(int $userID, int $skillID): bool
    {
        $key = $this->generateUserSkills($userID);
        $existSkillIDs = $this->getUserSkills($userID);
        if (empty($existSkillIDs)) {
            return true;
        }

        foreach ($existSkillIDs as $item) {
            if ($item == $skillID) {
                return $this->redis->sRem($key, $skillID);
            }
        }
    }

    public function delUserSkills(int $userID): bool
    {
        $key = $this->generateUserSkills($userID);
        return $this->redis->del($key);
    }

    private function generateUserSkills(string $id): string
    {
        return 'user:skills:' . $id;
    }

    private function updateSet(string $key, array $newMembers): ?array
    {
        $newMembers = array_unique($newMembers);
        $exist = $this->redis->sMembers($key);
        $this->redis->multi();
        if (!empty($exist)) {
            $this->redis->del($key);
        }
        foreach ($newMembers as $id) {
            $this->redis->sAdd($key, $id);
        }
        return $this->redis->exec();
    }

    private function getSet(string $key): ?array
    {
        $result = $this->redis->sMembers($key);
        if (empty($result)) {
            return null;
        }
        return $result;
    }

    private function setHash(string $key, Model $model): bool
    {
        $model = $model->toArray();
        unset($model['id']);
        try {
            return $this->redis->hMSet($key, $model);
        } catch (\Exception $e) {
            throw new RedisDAOException($e->getMessage(), $e->getCode());
        }
    }
}
