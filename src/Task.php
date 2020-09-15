<?php
namespace EasyTask;

/**
 * Class Task
 * @package EasyTask
 */
class Task
{
    /**
     * Collection
     * @var array
     */
    private static $collection;

    /**
     * add
     * @param array $task
     * @return int
     */
    public static function add($task)
    {
        $taskId = count(self::$collection) + 1;
        self::$collection[$taskId] = $task;
        return $taskId;
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