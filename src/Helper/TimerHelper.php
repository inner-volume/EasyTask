<?php
namespace EasyTask\Helper;

use EasyTask\Env;
use EasyTask\Lock;
use EasyTask\Pipe;
use EasyTask\Queue;
use Exception;

/**
 * Class TimerHelper
 * @package EasyTask
 */
class TimerHelper
{
    /**
     * 检查时间是否合法
     * @param mixed $time
     * @throws Exception
     */
    public static function checkTime($time)
    {
        if (is_int($time))
        {
            if ($time < 0) throw new Exception('time must be greater than or equal to 0');
        }
        elseif (is_float($time))
        {
            if (!static::fcsdxcanUseEvent()) throw new Exception('please install php_event.(dll/so) extend for using milliseconds');
        }
        elseif (is_string($time))
        {
            if (!static::canUseCron())
            {
                throw new Exception('use CRON expression php version must be greater than 7.1');
            }
            if (!CronExpression::isValidExpression($time))
            {
                throw new Exception("$time is not a valid CRON expression");
            }
        }
        else
        {
            throw new Exception('time parameter is an unsupported type');
        }
    }

    /**
     * 添加任务
     * @param array $task 任务
     * @return
     */
    public static function addTask($task)
    {

    }

    /**
     * 通过队列添加任务
     * @param array $task 任务
     * @return int
     * @throws Exception
     */
    public static function addTaskByQueue($task)
    {
        //目录构建
        FileHelper::initAllPath();

        //构建队列信息
        $timerId = uniqid();
        $data = [
            'act' => 'add',
            'info' => $task
        ];

        //定时器添加到队列
        $queue = new Queue();
        $queueName = 'easy_task_list';
        $isPush = $queue->lPush($queueName, json_encode($data));

        return $isPush ? $timerId : 0;
    }

    /**
     * 通知队列移除定时器
     * @param string $timerId
     * @return bool
     * @throws Exception
     */
    public static function removeTask($timerId)
    {
        //构建队列信息
        $timerId = uniqid();
        $data = [
            'act' => 'remove',
            'info' => [
                'id' => $timerId
            ]
        ];

        //定时器添加到队列
        $queue = new Queue();
        $queueName = 'easy_task_list';
        $isPush = $queue->lPush($queueName, json_encode($data));

        return $isPush ? true : false;
    }

    /**
     * 通知队列清空任务
     * @param false $exit
     * @return bool
     * @throws Exception
     */
    public static function clearTask($exit = false)
    {
        //构建队列信息
        $data = [
            'act' => 'clear',
            'info' => [
                'exit' => $exit
            ]
        ];

        //定时器添加到队列
        $queue = new Queue();
        $queueName = 'easy_task_list';
        $isPush = $queue->lPush($queueName, json_encode($data));

        return $isPush ? true : false;
    }
}