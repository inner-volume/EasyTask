<?php
namespace EasyTask\Helper;

use EasyTask\Cache;
use EasyTask\Helper;
use EasyTask\Timer;
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
     * 获取执行时间
     * @param mixed $time 时间
     * @param mixed $nextTime 原执行时间
     * @return int
     */
    public static function getNextTime($time, $nextTime = 0)
    {
        if (is_int($time))
        {
            return $nextTime ? $nextTime + $time : time() + $time;
        }
        else
        {
            $nextTime = $nextTime ? date('Y-m-d H:i:s', $nextTime) : 'now';
            $cronExpression = CronExpression::factory($time);
            $nextExecDate = $cronExpression->getNextRunDate($nextTime)->format('Y-m-d H:i:s');
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
     * @return string
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
            'next_time' => self::getNextTime($time),
            'persistent' => $persistent
        ];
        if (Helper::isCli())
        {
            Timer::set($tid, $task);
        }
        else
        {
            self::addTaskByQueue($task);
        }
        return $tid;
    }

    /**
     * 通过队列添加任务
     * @param array $task 任务
     * @throws Exception
     */
    public static function addTaskByQueue($task)
    {
        //目录构建
        FileHelper::initAllPath();

        //添加到队列
        $queue = new Cache();
        $queueName = 'easy_task_list';
        $isPush = $queue->lPush($queueName, json_encode([
            'act' => 'add',
            'info' => $task
        ]));
        if (!$isPush)
        {
            throw new Exception('failed to push task to queue');
        }
    }

    /**
     * 移除定时器
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

        if (Helper::isCli())
        {
            Timer::remove($timerId);
        }
        else
        {

        }

        //定时器添加到队列
        $queue = new Cache();
        $queueName = 'easy_task_list';
        $isPush = $queue->lPush($queueName, json_encode($data));

        return $isPush ? true : false;
    }

    /**
     * 通知队列移除定时器
     * @param string $timerId
     * @throws Exception
     */
    public static function removeTaskByQueue($timerId)
    {
        //目录构建
        FileHelper::initAllPath();

        //添加到队列
        $queue = new Cache();
        $queueName = 'easy_task_list';
        $isPush = $queue->lPush($queueName, json_encode([
            'act' => 'remove',
            'info' => [
                'id' => $timerId
            ]
        ]));
        if (!$isPush)
        {
            throw new Exception('failed to push task to queue');
        }
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