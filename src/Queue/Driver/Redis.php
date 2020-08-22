<?php
namespace EasyTask\Queue\Driver;

use EasyTask\Queue\Driver;

/**
 * Class File
 * @package EasyTask
 */
class Redis extends Driver
{
    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->handler = new \Redis();
        if ($this->options['persistent'])
        {
            $this->handler->pconnect($this->options['host'], $this->options['port'], $this->options['timeout'], 'persistent_id_' . $this->options['select']);
        }
        else
        {
            $this->handler->connect($this->options['host'], $this->options['port'], $this->options['timeout']);
        }
        if ('' != $this->options['password'])
        {
            $this->handler->auth($this->options['password']);
        }
        if (0 != $this->options['select'])
        {
            $this->handler->select($this->options['select']);
        }
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
