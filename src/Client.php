<?php
namespace EasyTask;

use \Closure as Closure;
use Exception;

/**
 * Class Client
 * @package EasyTask
 */
class Client
{
    /**
     * 设置服务端名称
     * @param string $name
     * @return $this
     */
    public function setServerName($name = 'task')
    {
        Env::set('name', $name);
        return $this;
    }

    /**
     * 设置服务端目录
     * @param string $path
     * @return $this
     * @throws Exception
     */
    public function setServerPath($path = '')
    {
        if (!is_dir($path))
        {
            throw new Exception("the path {$path} is not exist");
        }
        if (!is_writable($path))
        {
            throw new Exception("the path {$path} is not writeable");
        }
        Env::set('path', realpath($path));
        return $this;
    }

    /**
     * 新增任务
     * @param Closure $func 匿名函数
     * @param string $alas 任务别名
     * @param mixed $time 定时器间隔
     * @param bool $persistent 持续执行
     * @return int 返回定时器Id
     * @throws
     */
    public function add($func, $alas, $time = 1, $persistent = true)
    {
        if (!($func instanceof Closure))
        {
            throw new Exception('the func parameter must be a closure function');
        }
        return 1;
    }

    /**
     * 删除任务
     * @param int $taskId 任务Id
     * @return bool
     * @throws
     */
    public function remove($taskId)
    {
        return 1;
    }

    /**
     * 清空任务
     * @return bool
     */
    public function clear()
    {
        return true;
    }
}