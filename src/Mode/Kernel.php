<?php
namespace EasyTask\Mode;

use EasyTask\Env;
use EasyTask\Error;
use EasyTask\Helper;
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
     * 构造函数
     */
    public function __construct()
    {
        $this->startTime = time();
    }

    /**
     * 开始运行
     */
    public function start()
    {

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
     * 管理进程(同步:分发进程+响应状态 异步:响应状态+创建调度进程)
     * @return mixed
     */
    public function manager()
    {

    }

    /**
     * 调度进程(异步:分发进程)
     * @return mixed
     */
    public function scheduler()
    {

    }
}