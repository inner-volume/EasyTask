<?php
namespace EasyTask\Helper;

class ProcessHelper
{
    /**
     * canUseEvent
     * @return bool
     */
    public static function canUseEvent()
    {
        return (extension_loaded('event'));
    }

    /**
     * canUseAsyncSignal
     * @return bool
     */
    public static function canUseAsyncSignal()
    {
        return (function_exists('pcntl_async_signals'));
    }
}

