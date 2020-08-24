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
            if ($time < 0) throw new Exception('the time parameter must be a positive number');
        }
        elseif (is_float($time))
        {
            throw new Exception('the time parameter does not support decimals');
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

    public static function nextTime($time)
    {

    }

    /**
     * 添加任务
     * @param $class
     * @param $func
     * @param $alas
     * @param int $time
     * @param bool $persistent
     * @throws Exception
     */
    public static function addTask($class, $func, $alas, $time = 1, $persistent = true)
    {
        //检查时间
        self::checkTime($time);

        //创建任务Id
        $tid = uniqid();
        $task = [
            'id' => $tid,
            'func' => $func,
            'alas' => $alas,
            'time' => $time,
            'class' => $class,
            'persistent' => $persistent
        ];
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