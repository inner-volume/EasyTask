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
     * 自动初始化缓存
     * @access public
     * @param array $options 配置数组
     * @return Driver
     */
    public static function init(array $options = [])
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


}