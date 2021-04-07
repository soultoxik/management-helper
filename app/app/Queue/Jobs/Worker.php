<?php


namespace App\Queue\Jobs;

use App\Helpers\JSONHelper;
use App\Models\Group;
use App\Models\Student;

class Worker
{
    protected const REQUIRED_PARAM = ['request_id', 'data'];

    protected int $requestID;
    protected string $command;
    private int $id;

    public function __construct(string $message, string $command)
    {
        $this->validate($message);

        $data = json_decode($message, true);
        $this->command = $command;
        $this->requestID = $data['request_id'];
        $this->id = $data['id'];
    }

    public function createJob(): Job
    {
        switch ($this->command) {
            case 'create_group':
                $group = Group::where('id', $this->id)->first();
                // Но надо будет пользоваться GroupRepository
                // $groupID = $this->id;
                // $group = загрузка Group из БД
                 $job = new JobCreateGroup($group);
                break;
            case 'find_teacher':
                $group = Group::where('id', $this->id)->first();
                // Но надо будет пользоваться GroupRepository
                // $groupID = $this->id;
                // $group = загрузка Group из БД
                 $job = new JobFindTeacher($group);
                break;
            case 'find_group_new_user':
                $student = Student::where('id', $this->id)->first();
                // Но надо будет пользоваться StudentRepository
                // $studentID = $this->id;;
                // $student = загрузка Student из БД
                 $job = new JobFindGroupNewUser($student);
                break;
            case 'replace_teacher':
                $group = Group::where('id', $this->id)->first();
                // Но надо будет пользоваться GroupRepository
                // $groupID = $this->id;
                // $group = загрузка Group из БД
                 $job = new JobReplaceTeacher($group);
                break;
            default:
                // Exception:: 'работа не объявлена'
                break;
        }
//        AppLogger::addInfo('RabbitMQ:Consumer create job - ' . $this->command);
        return $job;
    }

    public function finish()
    {
//        Request::update(['id' => $this->requestID, 'status' => 'Done']);
    }

    private function validate(string $message)
    {
        if (!JSONHelper::isJSON($message)) {
            // Exception:: 'получен не JSON'
        }
        $data = json_decode($message, true);
        foreach (self::REQUIRED_PARAM as $item) {
            if (empty($data[$item])) {
                // Exception:: 'Обязательных параметров нету в сообщении'
            }
        }
    }
}
