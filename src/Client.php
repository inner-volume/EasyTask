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
     * Client constructor.
     * @param string $server_name
     * @param string $server_path
     */
    public function __construct($server_name = 'task', $server_path = '')
    {
        Env::set('name', $server_name);
        Env::set('run_path', $server_path ? $server_path : sys_get_temp_dir());
        $server_path = Helper::getRunTimePath();
    }

    /**
     * 查看任务
     * @param array
     */
    public function get()
    {

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