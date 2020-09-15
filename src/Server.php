<?php
namespace EasyTask;

use \Closure as Closure;
use EasyTask\Process\Linux;
use EasyTask\Process\Win;
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
     * 异常通知
     * @param string|Closure $notify
     * @return $this
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
     * 获取进程管理实例
     * @return  Win | Linux
     */
    private function getProcess()
    {
        if (Helper::isWin()){
            return (new Win());
        }
        else{
            return (new Linux());
        }
    }

    /**
     * 开始运行
     * @throws
     */
    public function start()
    {
        //异常注册
        if (Env::get('error_register')) Error::register();

        //目录构建
        Helper::initAllPath();

        //进程启动
        $process = $this->getProcess();
        $process->start();
    }

    /**
     * 运行状态
     * @throws
     */
    public function status()
    {
        $process = $this->getProcess();
        $process->status();
    }

    /**
     * 停止运行
     * @param bool $force 是否强制
     * @throws
     */
    public function stop($force = false)
    {
        $process = $this->getProcess();
        $process->stop($force);
    }
}