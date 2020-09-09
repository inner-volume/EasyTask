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
     * 服务端端口
     * @var string
     */
    private $port = null;

    /**
     * 设置服务端端口
     * @param int $port
     */
    public function setPort($port = 9501)
    {
        $this->port = $port;
    }

    /**
     * 新增任务
     * @param string $name 任务名称
     * @param Closure $func 任务函数
     * @param int $time 任务间隔
     * @param bool $persistent 持续执行
     * @return int 任务Id
     * @throws
     */
    public function addTask($name, $func, $time = 1, $persistent = true)
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
    public function removeTask($taskId)
    {
        return 1;
    }

    /**
     * 清空任务
     * @return bool
     */
    public function clearTask()
    {
        return true;
    }
}