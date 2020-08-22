<?php
namespace EasyTask\Queue;

/**
 * Class Driver
 * @package EasyTask
 */
abstract class Driver
{
    /**
     * @var null
     */
    protected $handler = null;

    /**
     * @var array
     */
    protected $options = [];


    /**
     * 获取列表头部元素
     * @param string $key 缓存Key
     * @return string|bool
     */
    abstract public function rPop($key);

    /**
     * 插入元素到列表头部
     * @access public
     * @param string $key 缓存Key
     * @param string $value 缓存Value
     * @return boolean
     */
    abstract public function lPush($key, $value);
}
