<?php


namespace App\Queue\Jobs;

use App\Helper;

class Worker
{
    const ALLOWED_COMMANDS = ['create_group', 'find_teacher', 'find_group_new_user', 'replace_teacher'];
    const REQUIRED_PARAM = ['request_id', 'command', 'data'];

    protected int $requestID;
    protected string $command;
    private array $data;

    public function __construct(string $message)
    {
        $this->validate($message);

        $data = json_decode($message, true);
        $this->command = $data['command'];
        $this->requestID = $data['request_id'];
        $this->data = $data['data'];
    }

    public function create(): Job
    {
        switch ($this->command) {
            case 'create_group':
                // $groupID = $this->data['group_id'];
                // $group = загрузка Group из БД
                // $job = new JobCreateGroup($group);
                break;
            case 'find_teacher':
                // $groupID = $this->data['group_id'];
                // $group = загрузка Group из БД
                // $job = new JobFindTeacher($group);
                break;
            case 'find_group_new_user':
                // $studentID = $this->data['student_id'];
                // $student = загрузка Student из БД
                // $job = new JobFindGroupNewUser($student);
                break;
            case 'replace_teacher':
                // $groupID = $this->data['group_id'];
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

        if (!in_array($data['command'], self::ALLOWED_COMMANDS)) {
            // Exception:: 'Такая команда не поддеживается'
        }
    }
}