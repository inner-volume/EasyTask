<?php
namespace EasyTask\Helper;

use EasyTask\Cache;
use EasyTask\Helper;
use Exception;

/**
 * Class TimerHelper
 * @package EasyTask
 */
class TimerHelper
{
    /**
     * 检查时间
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
                throw new Exception('if the time parameter needs to be passed cron, please use composer to install dragonmantank/cron-expression');
            }
            if (!CronExpression::isValidExpression($time))
            {
                throw new Exception("the time $time is not a valid cron expression");
            }
        }
        else
        {
            throw new Exception('the time parameter does not support this type');
        }
    }

    /**
     * 获取下一轮执行时间
     * @param mixed $time 时间
     * @param mixed $execTime 执行时间
     * @return int
     */
    public static function getNextExecTime($time, $execTime = 0)
    {
        if (is_int($time))
        {
            return $execTime ? $execTime + $time : time() + $time;
        }
        else
        {
            $cronExpression = CronExpression::factory($time);
            $nextExecDate = $cronExpression->getNextRunDate('now')->format('Y-m-d H:i:s');
            return $nextExecDate ? strtotime($nextExecDate) : 0;
        }
    }

    /**
     * 是否支持Cron
     * @return bool
     */
    public static function canUseCron()
    {
        $class = '';
        return class_exists($class);
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
        $queue = new Cache();
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
        $queue = new Cache();
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
        $queue = new Cache();
        $queueName = 'easy_task_list';
        $isPush = $queue->lPush($queueName, json_encode($data));

        return $isPush ? true : false;
    }
}