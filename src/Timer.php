<?php
namespace EasyTask;

/**
 * Class Timer
 * @package EasyTask
 */
class Timer
{
    /**
     * Collection[$timerId=>$task]
     * @var array
     */
    private static $collection;

    /**
     * Set
     * @param string $timerId
     * @param array $task
     */
    public static function set($timerId, $task)
    {
        //todo() exec_time();
        self::$collection[$timerId] = $task;
    }

    /**
     * Clear
     */
    public static function clear()
    {
        self::$collection = [];
    }
}