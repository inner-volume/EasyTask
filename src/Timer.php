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
     * @param string $timerId
     * @param array $task
     */
    public static function set($timerId, $task)
    {
        self::$collection[$timerId] = $task;
    }

    /**
     * Get
     */
    public static function get()
    {
        $times = [];
        $tasks = self::$collection;
        foreach ($tasks as $key => $value)
        {
            $times[$key] = $value['next_time'];
        }
        array_multisort($times, SORT_ASC, $tasks);
        return empty($tasks['0']) ? [] : $tasks['0'];
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