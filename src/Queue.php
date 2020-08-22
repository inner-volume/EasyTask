<?php
namespace EasyTask;

use EasyTask\Queue\Driver;

class Queue
{
    /**
     * @var object 操作句柄
     */
    public static $handler;

    /**
     * 自动初始化
     * @return Driver
     */
    public static function init()
    {
        //提取配置
        $config = Env::get('queue_config');
        $class = '\\EasyTask\\Queue\\Driver\\' . ucwords($config['driver']);
        if (!self::$handler)
        {
            self::$handler = new $class($config['options']);
        }

        return self::$handler;
    }

    /**
     * rPop
     * @param string $key
     * @return bool|mixed|string
     */
    public function rPop($key)
    {
        return self::init()->rPop($key);
    }

    /**
     * lPush
     * @param string $key
     * @param string $value
     * @return bool|int
     */
    public function lPush($key, $value)
    {
        return self::init()->lPush($key, $value);
    }
}