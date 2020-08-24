<?php
namespace EasyTask;

use EasyTask\Cache\Driver;
use Exception;

class Cache
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
     * @throws Exception
     */
    public function lPush($key, $value)
    {
        $config = Env::get('queue_config');
        if ($config['driver'] === 'file')
        {
            $lock = new Lock($key);
            $result = $lock->execute(function () use ($key, $value) {
                return self::init()->lPush($key, $value);
            }, true);
        }
        else
        {
            $result = self::init()->lPush($key, $value);
        }
        return $result;
    }
}