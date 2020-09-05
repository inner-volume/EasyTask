<?php
namespace EasyTask;

/**
 * Class Timer
 * @package EasyTask
 */
class Timer
{
    /**
     * Collection
     * @var array
     */
    private static $collection;

    /**
     * Set
     * @param array $task
     * @return int
     */
    public static function set($task)
    {
        $timerId = count(self::$collection) + 1;
        self::$collection[$timerId] = $task;
        return $timerId;
    }

    /**
     * Get
     */
    public static function get()
    {
        return self::$collection;
    }

    /**
     * Change
     * @param $timerId
     * @param $key
     * @param $value
     */
    public static function change($timerId, $key, $value)
    {
        if (isset(self::$collection[$timerId]))
        {
            self::$collection[$timerId][$key] = $value;
        }
    }

    /**
     * remove
     * @param string $timerId
     */
    public static function remove($timerId)
    {
        unset(self::$collection[$timerId]);
    }

    /**
     * Clear
     */
    public static function clear()
    {
        self::$collection = [];
    }
}