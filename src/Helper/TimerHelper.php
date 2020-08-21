<?php
namespace EasyTask\Helper;
;

/**
 * Class TimerHelper
 * @package EasyTask
 */
class TimerHelper
{
    /**
     * 检查时间是否合法
     * @param mixed $time
     * @throws
     */
    public static function checkTime($time)
    {
        if (is_int($time))
        {
            if ($time < 0) throw new \Exception('time must be greater than or equal to 0');;
        }
        elseif (is_float($time))
        {
            if (!static::canUseEvent()) static::showSysError('please install php_event.(dll/so) extend for using milliseconds');
        }
        else
        {
            throw new \Exception('time parameter is an unsupported type');
        }
    }
}