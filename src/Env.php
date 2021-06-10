<?php
namespace EasyTask;

/**
 * Class Env
 * @package EasyTask
 */
class Env
{

    /**
     * collection
     * @var array
     */
    private static $collection;

    /**
     * set
     * @param string $key
     * @param mixed $value
     */
    public static function set($key, $value)
    {
        static::$collection[$key] = $value;
    }

    /**
     * get
     * @param string $key
     * @return mixed
     */
    public static function get($key)
    {
        return isset(static::$collection[$key]) ? static::$collection[$key] : false;
    }
}