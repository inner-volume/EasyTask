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
     * 添加定时器到队列
     * @param string $class 类名称
     * @param string $func 方法名称
     * @param string $alas 任务别名
     * @param mixed $time 定时器间隔
     * @param int $used 定时器占用进程数
     * @param bool $persistent 持续执行
     * @return bool
     * @throws Exception
     */
    public static function addTask($class, $func, $alas, $time = 1, $used = 1, $persistent = true)
    {
        //检查定时器时间
        TimerHelper::checkTime($time);

        //构建队列信息
        $timerId = uniqid();
        $data = [
            'act' => 'add',
            'info' => [
                'id' => $timerId,
                'func' => $func,
                'alas' => $alas,
                'time' => $time,
                'used' => $used,
                'class' => $class,
                'persistent' => $persistent
            ]
        ];

        //定时器添加到队列
        $queue = new Queue();
        $queueName = 'easy_timer_list';
        $queueConfig = Env::get('queue_config');
        if ($queueConfig['driver'] === 'file')
        {
            $lock = new Lock($queueName);
            $isPush = $lock->execute(function () use ($queue, $data, $queueName) {

                return $queue->lPush($queueName, json_encode($data));
            }, true);
        }
        else
        {
            $isPush = $queue->lPush($queueName, json_encode($data));
        }
        return $isPush ? $timerId : 0;
    }

    /**
     * 通知队列移除定时器
     * @param string $timerId
     * @return int|string
     * @throws Exception
     */
    public static function removeTimer($timerId)
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
        $queueName = 'easy_timer_list';
        $queueConfig = Env::get('queue_config');
        if ($queueConfig['driver'] === 'file')
        {
            $lock = new Lock($queueName);
            $isPush = $lock->execute(function () use ($queue, $data, $queueName) {

                return $queue->lPush($queueName, json_encode($data));
            }, true);
        }
        else
        {
            $isPush = $queue->lPush($queueName, json_encode($data));
        }
        return $isPush ? $timerId : 0;
    }
}