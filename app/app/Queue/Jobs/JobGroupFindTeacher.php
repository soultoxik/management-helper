<?php


namespace App\Queue\Jobs;


use App\Exceptions\GroupException;
use App\Logger\AppLogger;
use App\Models\Teacher;
use App\Repository\GroupRepository;
use League\Route\Http\Exception\BadRequestException;
use League\Route\Http\Exception\NotFoundException;

class JobGroupFindTeacher extends Job
{
    const FAIL = 'not_found_teacher_for_group';

    private Teacher $teacher;
    private GroupRepository $groupRepo;
    private int $groupID;

    public function __construct(int $groupID)
    {
        parent::__construct();
        $this->groupRepo = new GroupRepository($this->cache);
        $this->groupID = $groupID;
    }

    protected function work(): bool
    {
        try {
            $group = $this->groupRepo->getGroup($this->groupID);
            $user = $this->groupRepo->findSuitableTeacher($group);
            return $this->groupRepo->setTeacherID($group, $user->id);
        } catch (NotFoundException $e) {
            AppLogger::addWarning(
                'RabbitMQ:Consumer:' . $e->getMessage()
            );
        } catch (BadRequestException | GroupException $e) {
            AppLogger::addError(
                'RabbitMQ:Consumer:' . $e->getMessage()
            );
        }
        return false;
    }

    public function getStatusFail(): string
    {
        return self::FAIL;
    }
}