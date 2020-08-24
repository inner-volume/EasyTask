<?php
namespace EasyTask\Mode;

use EasyTask\Env;
use EasyTask\Error;
use EasyTask\Helper;
use EasyTask\Process\Linux;
use EasyTask\Process\Win;
use \Event as Event;
use \EventBase as EventBase;
use \EventConfig as EventConfig;
use \Exception as Exception;
use \Throwable as Throwable;

/**
 * Class Process
 * @package EasyTask\Process
 */
class Kernel
{
    /**
     * 任务列表
     * @var array
     */
    private $tasks;

    /**
     * 进程实例
     * @var Win|Linux
     */
    private $instance;

    /**
     * Kernel constructor.
     */
    public function __construct()
    {
        $this->instance = Helper::isWin() ? new Win() : new Linux();
    }

    /**
     * 开始运行
     * @throws Exception
     */
    public function start()
    {
        //模式检查
        if (!Helper::isCli())
        {
            throw new Exception('please use cli mode to start');
        }

        //异常注册
        if (Env::get('error_register')) Error::register();


    }

    /**
     * 运行状态
     */
    public function status()
    {

    }

    /**
     * 停止运行
     * @param bool $force 是否强制
     */
    public function stop($force = false)
    {

    }

    /**
     * 主进程
     * @return mixed
     */
    public function master()
    {

    }

    /**
     * 管理进程
     * @return mixed
     */
    public function manager()
    {

    }
}