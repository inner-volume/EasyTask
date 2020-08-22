<?php
namespace EasyTask\Helper;

use EasyTask\Lock;
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
            if (!static::canUseEvent()) throw new Exception('please install php_event.(dll/so) extend for using milliseconds');
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
     * 添加定时器到管道
     * @param array $timer
     */
    public static function addTimerToPipe($timer)
    {
        $lock = new Lock();
    }
}