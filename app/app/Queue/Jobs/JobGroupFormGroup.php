<?php


namespace App\Queue\Jobs;

use App\Exceptions\AppException;
use App\Exceptions\GroupRepositoryException;
use App\Logger\AppLogger;
use App\Models\Group;
use App\Repository\GroupRepository;
use League\Route\Http\Exception\BadRequestException;
use League\Route\Http\Exception\NotFoundException;

class JobGroupFormGroup extends Job
{
    const FAIL = 'not_formed_group';

    private Group $group;
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
            $result = $this->groupRepo->formGroup($group);
            $studentIDs = [];
            foreach ($result as $item) {
                $studentIDs[] = $item->user_id;
            }
            return $this->groupRepo->setStudentsByGroupID($group->id, $studentIDs);
        } catch (AppException | NotFoundException $e) {
            AppLogger::addWarning(
                'RabbitMQ:Consumer:' . $e->getMessage()
            );
        } catch (BadRequestException | GroupRepositoryException $e) {
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