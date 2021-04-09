<?php


namespace App\Queue\Jobs;

use App\Exceptions\GroupException;
use App\Logger\AppLogger;
use App\Repository\StudentRepository;
use App\Models\Student;
use League\Route\Http\Exception\BadRequestException;
use League\Route\Http\Exception\NotFoundException;

class JobStudentFindGroup extends Job
{
    const FAIL = 'not_found_group_for_student';

    private Student $student;
    private StudentRepository $studentRepo;
    private int $studentID;

    public function __construct(int $studentID)
    {
        parent::__construct();
        $this->studentRepo = new StudentRepository($this->cache);
        $this->studentID = $studentID;
    }

    protected function work(): bool
    {
        try {
            $this->student = $this->studentRepo->getStudentByID($this->studentID);
            $group = $this->studentRepo->findSuitableGroup($this->student->user);
            return $this->studentRepo->addGroup($this->student->user->id, $group->id);
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