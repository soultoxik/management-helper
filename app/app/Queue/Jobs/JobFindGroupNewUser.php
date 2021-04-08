<?php


namespace App\Queue\Jobs;

use App\Logger\AppLogger;
use App\Repository\GroupRepository;
use App\Repository\StudentRepository;
use App\Models\Student;
use App\Storage\RedisDAO;

class JobFindGroupNewUser extends Job
{
    private Student $student;

    public function __construct(Student $student)
    {
        $this->student = $student;
    }

    public function work(): bool
    {
        $repo = new StudentRepository(new RedisDAO());
        $group = $repo->findSuitableGroup($this->student->user);
        if (empty($group)) {
            AppLogger::addInfo(
                'RabbitMQ:Consumer - Could not find groups for student: ' . $this->student->user->id
            );
            return false;
        }
        // находит и если еще раз отправляю тоже самое. то опять находит туже группу
        // но запись не добавляется в бд( я так понимаю не проходит запись на уровне команды sync)
        // потому что исключения не выпадают
        $result = $repo->addGroup($this->student->user->id, $group->id);
        $status = $result ? ' was ': ' was not ';
        AppLogger::addInfo(
            'RabbitMQ:Consumer - For student: ' . $this->student->user->id
            . $status . 'found groupID:' . $group->id
        );
        return $result;
    }
}