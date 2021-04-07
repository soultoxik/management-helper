<?php


namespace App\Queue\Jobs;

use App\Helper;
use App\Repository\UserRepository;

class Worker
{
    protected const REQUIRED_PARAM = ['request_id', 'data'];

    const COMMAND_CREATE_GROUP = 'create_group';
    const COMMAND_FIND_TEACHER = 'find_teacher';
    const COMMAND_FIND_GROUP_NEW_USER = 'find_group_new_user';
    const COMMAND_REPLACE_TEACHER = 'replace_teacher';

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
            case self::COMMAND_CREATE_GROUP:
                // $groupID = $this->id;
                // $group = загрузка Group из БД
                // $job = new JobCreateGroup($group);
                break;
            case self::COMMAND_FIND_TEACHER:
                // $groupID = $this->id;
                // $group = загрузка Group из БД
                // $job = new JobFindTeacher($group);
                break;
            case self::COMMAND_FIND_GROUP_NEW_USER:
                 $student = (new UserRepository())->findById($this->id);
                 $job = new JobFindGroupNewUser($student);
                break;
            case self::COMMAND_REPLACE_TEACHER:
                // $groupID = $this->id;
                // $group = загрузка Group из БД
                // $job = new JobReplaceTeacher($group);
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
        if (!Helper::isJSON($message)) {
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