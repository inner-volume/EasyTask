<?php
namespace EasyTask\Queue\Driver;

use EasyTask\Queue\Driver;

/**
 * Class File
 * @package EasyTask
 */
class RedisCluster extends Driver
{
    /**
     * 默认配置
     * @var array
     */
    protected $options = [
        '127.0.0.1:7000',
        '127.0.0.1:7001',
        '127.0.0.1:7002',
        '127.0.0.1:7003',
        '127.0.0.1:7004'
    ];

    /**
     * 构造函数
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->options = array_merge($this->options, $options);
        $this->handler = new \RedisCluster();
    }

    /**
     * rPop
     * @param string $key
     * @return bool|mixed|string
     */
    public function rPop($key)
    {
        return $this->handler->rPop($key);
    }

    /**
     * lPush
     * @param string $key
     * @param string $value
     * @return bool|int
     */
    public function lPush($key, $value)
    {
        return $this->handler->lPush($key, $value);
    }
}
