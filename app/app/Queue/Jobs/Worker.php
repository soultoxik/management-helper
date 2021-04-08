<?php


namespace App\Queue\Jobs;

use App\Logger\AppLogger;
use App\Repository\RequestRepository;
use App\Exceptions\WorkerException;
use App\Helpers\JSONHelper;
use App\Models\Group;
use App\Repository\GroupRepository;
use App\Repository\StudentRepository;
use App\Storage\RedisDAO;

class Worker
{
    protected const REQUIRED_PARAM = ['request_id', 'id'];

    public const COMMAND_CREATE_GROUP = 'create_group';
    public const COMMAND_FIND_TEACHER = 'find_teacher';
    public const COMMAND_FIND_GROUP_NEW_USER = 'find_group_new_user';
    public const COMMAND_REPLACE_TEACHER = 'change_teacher';

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

    /**
     * @return Job
     * @throws WorkerException
     */
    public function createJob(): Job
    {
        $redis = new RedisDAO();
        $msgPrefix = 'Command (' . $this->command . ') can not do.';
        switch ($this->command) {
            case 'create_group':
                $group = $this->prepareJobGroup($this->id, $redis, $msgPrefix);
                $job = new JobCreateGroup($group);
                break;
            case 'change_teacher':
            case 'find_teacher':
                $group = $this->prepareJobGroup($this->id, $redis, $msgPrefix);
                $job = new JobFindTeacher($group);
                break;
            case 'find_group_new_user':
                $repo = new StudentRepository(new RedisDAO());
                $student = $repo->getStudentByID($this->id);
                if (empty($student)) {
                    throw new WorkerException(
                        $msgPrefix . ' Can not load student: ' . $this->id
                    );
                }
                $job = new JobFindGroupNewUser($student);
                break;
            case 'change_teacher':
                $group = $this->prepareJobGroup($this->id, $redis, $msgPrefix);
                $job = new JobChangeTeacher($group);
                break;
            default:
                throw new WorkerException('This command is not available.');
        }
        AppLogger::addInfo('RabbitMQ:Consumer create job - ' . $this->command);
        return $job;
    }

    /**
     * @param int      $groupID
     * @param RedisDAO $redis
     * @param string   $msg
     *
     * @return Group
     * @throws WorkerException
     */
    private function prepareJobGroup(int $groupID, RedisDAO $redis, string $msg): Group
    {
        $repo = new GroupRepository(new RedisDAO());
        $group = $repo->getGroup($this->id);
        if (empty($group)) {
            throw new WorkerException(
                $msg . ' Can not load group: ' . $this->id
            );
        }
        return $group;
    }

    /**
     * @param string $message
     *
     * @throws WorkerException
     */
    private function validate(string $message): void
    {
        if (!JSONHelper::isJSON($message)) {
            throw new WorkerException('String is not format-JSON.');
        }
        $data = json_decode($message, true);
        foreach (self::REQUIRED_PARAM as $item) {
            if (empty($data[$item])) {
                throw new WorkerException('There is no required parameter: ' . $item . '.');
            }
        }
    }

    public function completed(Job $job): void
    {
        RequestRepository::setStatus($this->requestID, $job->getStatusSuccess());
    }

    public function fail(Job $job): void
    {
        RequestRepository::setStatus($this->requestID, $job->getStatusFail());
    }
}
