<?php
namespace EasyTask\Helper;

use EasyTask\Lock;
use EasyTask\Pipe;
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
     * @param array $timer
     * @return bool
     * @throws
     */
    public static function addTimer($timer)
    {
        return;

        //加锁管道
        $name = 'timer_queue';
        $lock = new Lock($name);
        $pipe = new Pipe($name);

        //处理消息
        $timer = base64_encode(json_encode($timer)) . PHP_EOL;
        $isWrite = $lock->execute(function () use ($pipe, $timer) {
            return $pipe->write($timer);
        }, true);

        return (bool)$isWrite;
    }

    /**
     *
     * @param string $timerId
     */
    public static function delTimer($timerId)
    {

    }
}