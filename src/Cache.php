<?php
namespace EasyTask;

/**
 * Class Cache
 * @package EasyTask
 */
abstract class Cache
{
    /**
     * 判断缓存是否存在
     * @param string $name 缓存变量名
     * @return bool
     */
    abstract public function has($name);

    /**
     * 读取缓存
     * @param string $name 缓存变量名
     * @param mixed  $default 默认值
     * @return mixed
     */
    abstract public function get($name, $default = false);

    /**
     * 写入缓存
     * @param string    $name 缓存变量名
     * @param mixed     $value  存储数据
     * @param int       $expire  有效时间 0为永久
     * @return boolean
     */
    abstract public function set($name, $value, $expire = null);
    abstract public function hSet($name, $value, $expire = null);
    abstract public function hGet($name, $value, $expire = null);
}
