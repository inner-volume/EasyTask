<?php
namespace EasyTask;

use \Closure as Closure;
use Exception;

/**
 * Class Server
 * @package EasyTask
 */
class Server
{
    /**
     * 构造函数
     */
    public function __construct()
    {
        //检查运行环境
        Check::analysis();

        //初始化基础配置
        Env::set('name', 'easy-task');
        Env::set('work_pool', 1);
        if (Helper::isWin()){
            Helper::setPhpPath();
            Helper::setCodePage();
        }
    }

    /**
     * 设置服务端口
     * @param int $port
     * @return Server
     */
    public function setPort($port = 9501)
    {
        Env::set('port', $port);
        return $this;
    }

    /**
     * 设置服务鉴权
     * @param string $auth
     * @return Server
     */
    public function setAuth($auth = '123456')
    {
        Env::set('auth', $auth);
        return $this;
    }

    /**
     * 设置是否后台运行
     * @param bool $daemon
     * @return Server
     */
    public function setDaemon($daemon = false)
    {
        Env::set('daemon', $daemon);
        return $this;
    }

    /**
     * 设置进程池数量
     * @param int $pool
     * @return Server
     */
    public function setWorkPool($pool = 1)
    {
        Env::set('work_pool', $pool);
        return $this;
    }

    /**
     * 新增任务
     * @param Closure $func 任务函数
     * @param int $time 任务间隔
     * @param bool $persistent 持续执行
     * @return int 任务Id
     * @throws
     */
    public function addTask($func, $time = 1, $persistent = true)
    {
        Helper::checkTaskTime($time);
        if (!($func instanceof Closure)){
            throw new Exception('the func parameter must be a closure function');
        }
        return Task::add(['func' => $func, 'time' => $time, 'persistent' => $persistent]);
    }

    /**
     * 异常通知
     * @param string|Closure $notify 通知地址|通知闭包函数逻辑
     * @return Server
     * @throws Exception
     */
    public function setErrorRegisterNotify($notify)
    {
        if (!$notify instanceof Closure && !is_string($notify)){
            throw new Exception('notify parameter can only be string or closure');
        }
        Env::set('error_register_notify', $notify);
        return $this;
    }

    /**
     * 开始运行
     * @throws
     */
    public function start()
    {
        //异常注册
        if (!Env::get('error_register')) Error::register();

        //目录构建
        Helper::initAllPath();

        //进程启动
        (Helper::get_process_loader())->start();
    }

    /**
     * 运行状态
     * @throws
     */
    public function status()
    {
        (Helper::get_process_loader())->status();
    }

    /**
     * 停止运行
     * @param bool $force 是否强制
     * @throws
     */
    public function stop($force = true)
    {
        (Helper::get_process_loader())->stop($force);
    }
}