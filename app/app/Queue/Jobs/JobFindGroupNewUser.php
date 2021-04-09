<?php


namespace App\Queue\Jobs;

use App\Logger\AppLogger;
use App\Repository\StudentRepository;
use App\Models\Student;
use App\Storage\RedisDAO;
use League\Route\Http\Exception\BadRequestException;
use League\Route\Http\Exception\NotFoundException;

class JobFindGroupNewUser extends Job
{
    const FAIL = 'not_found_group_for_user';

    private Student $student;

    public function __construct(Student $student)
    {
        $this->student = $student;
    }

    protected function work(): bool
    {
        try {
            $repo = new StudentRepository(new RedisDAO());
            $group = $repo->findSuitableGroup($this->student->user);
            return $repo->addGroup($this->student->user->id, $group->id);
        } catch (NotFoundException $e) {
            AppLogger::addWarning(
                'RabbitMQ:Consumer:' . $e->getMessage()
            );
        } catch (BadRequestException $e) {
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