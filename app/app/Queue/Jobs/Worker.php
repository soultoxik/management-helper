<?php


namespace App\Queue\Jobs;

use App\Logger\AppLogger;
use App\Repository\RequestRepository;
use App\Exceptions\WorkerException;
use App\Helpers\JSONHelper;
use App\Storage\RedisDAO;

class Worker
{
    protected const REQUIRED_PARAM = ['request_id', 'id'];

    public const COMMAND_STUDENT_FIND_GROUP = 'student_find_group';
    public const COMMAND_TEACHER_FIND_GROUP = 'teacher_find_group';
    public const COMMAND_GROUP_FIND_TEACHER = 'group_find_teacher';
    public const COMMAND_GROUP_CHANGE_TEACHER = 'group_change_teacher';
    public const COMMAND_GROUP_FORM_GROUP = 'group_form_group';

    protected int $requestID;
    protected string $command;
    private int $id;
    private RedisDAO $redis;

    public function __construct(string $message, string $command)
    {
        $this->validate($message);

        $data = json_decode($message, true);
        $this->command = $command;
        $this->requestID = $data['request_id'];
        $this->id = $data['id'];

        $this->redis = new RedisDAO();
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
            case self::COMMAND_STUDENT_FIND_GROUP:
//                $repo = new StudentRepository($this->redis);
//                $student = $repo->getStudentByID($this->id);
//                if (empty($student)) {
//                    throw new WorkerException(
//                        $msgPrefix . ' Can not load student: ' . $this->id
//                    );
//                }
                $job = new JobStudentFindGroup($this->id);
                break;
            case self::COMMAND_TEACHER_FIND_GROUP:
                $job = new JobTeacherFindGroup($this->id);
                break;
            case self::COMMAND_GROUP_FIND_TEACHER:
//                $repo = new TeacherRepository($this->redis);
//                $teacher = $repo->getTeacherByID($this->id);
                $job = new JobGroupFindTeacher($this->id);
                break;
            case self::COMMAND_GROUP_CHANGE_TEACHER:
//                $group = $this->prepareJobGroup($this->id, $redis, $msgPrefix);
                $job = new JobGroupChangeTeacher($this->id);
                break;
            case self::COMMAND_GROUP_FORM_GROUP:
//                $group = $this->prepareJobGroup($this->id, $redis, $msgPrefix);
                $job = new JobGroupFormGroup($this->id);
                break;
            default:
                throw new WorkerException('This command is not available.');
        }
        AppLogger::addInfo('RabbitMQ:Consumer create job - ' . $this->command);
        return $job;
    }

//    /**
//     * @param int      $groupID
//     * @param RedisDAO $redis
//     * @param string   $msg
//     *
//     * @return Group
//     * @throws WorkerException
//     */
//    private function prepareJobGroup(int $groupID, RedisDAO $redis, string $msg): Group
//    {
//        $repo = new GroupRepository($this->redis);
//        $group = $repo->getGroup($this->id);
//        if (empty($group)) {
//            throw new WorkerException(
//                $msg . ' Can not load group: ' . $this->id
//            );
//        }
//        return $group;
//    }

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
