<?php


namespace App\Queue\Jobs;

use App\Exceptions\GroupException;
use App\Logger\AppLogger;
use App\Models\Teacher;
use App\Repository\GroupRepository;
use App\Repository\TeacherRepository;
use League\Route\Http\Exception\BadRequestException;
use League\Route\Http\Exception\NotFoundException;

class JobTeacherFindGroup extends Job
{
    const FAIL = 'not_found_group_for_teacher';

    private Teacher $teacher;
    private TeacherRepository $teacherRepo;
    private int $teacherID;

    public function __construct(int $teacherID)
    {
        parent::__construct();
        $this->teacherRepo = new TeacherRepository($this->cache);
        $this->teacherID = $teacherID;
    }

    protected function work(): bool
    {
        try {
            $this->teacher = $this->teacherRepo->getTeacherByID($this->teacherID);
            $group = $this->teacherRepo->findSuitableGroup($this->teacher);
            $groupRepo = new GroupRepository($this->cache);
            return $groupRepo->setTeacherID($group, $this->teacher->user->id);
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