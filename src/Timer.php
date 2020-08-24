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
        self::$collection[$timerId] = $task;
    }
}