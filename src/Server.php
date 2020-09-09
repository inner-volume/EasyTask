<?php
namespace EasyTask;

use \Closure as Closure;
use EasyTask\Process\Linux;
use EasyTask\Process\Win;
use Exception;
use \ReflectionClass as ReflectionClass;
use \ReflectionMethod as ReflectionMethod;
use \ReflectionException as ReflectionException;

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
        $this->initialise();
    }

    /**
     * 进程初始化
     */
    private function initialise()
    {
        //初始化基础配置
        Env::set('prefix', 'task');
        Env::set('error_register', true);

        //初始化PHP_BIN|CODE_PAGE
        if (Helper::isWin())
        {
            Helper::setPhpPath();
            Helper::setCodePage();
        }
    }

    /**
     * 设置是否后台运行
     * @param bool $daemon
     * @return $this
     */
    public function setDaemon($daemon = false)
    {
        Env::set('daemon', $daemon);
        return $this;
    }

    /**
     * 设置任务前缀|项目名称
     * @param string $prefix
     * @return $this
     * @throws Exception
     */
    public function setPrefix($prefix = 'task')
    {
        if (Env::get('runtime_path'))
        {
            throw new Exception('should use setPrefix before setRunTimePath');
        }
        Env::set('prefix', $prefix);
        return $this;
    }

    /**
     * 设置PHP执行路径
     * @param string $path
     * @return $this
     * @throws Exception
     */
    public function setPhpPath($path)
    {
        $file = realpath($path);
        if (!file_exists($file))
        {
            throw new Exception("the path {$path} is not exists");
        }
        Helper::setPhpPath($path);
        return $this;
    }

    /**
     * 设置时区
     * @param string $timeIdent
     * @return $this
     * @throws Exception
     */
    public function setTimeZone($timeIdent)
    {
        if (!date_default_timezone_set($timeIdent))
        {
            throw new Exception('invalid timezone format');
        }
        return $this;
    }

    /**
     * 设置运行时目录
     * @param string $path
     * @return $this
     * @throws
     */
    public function setRunTimePath($path)
    {
        if (!is_dir($path))
        {
            throw new Exception("the path {$path} is not exist");
        }
        if (!is_writable($path))
        {
            throw new Exception("the path {$path} is not writeable");
        }
        Env::set('run_time_path', realpath($path));
        return $this;
    }

    /**
     * 设置关闭标准输出(关闭输出记录)
     * @param bool $close
     * @return $this
     */
    public function setCloseStdOut($close = false)
    {
        Env::set('close_std_out', $close);
        return $this;
    }

    /**
     * 设置异常注册
     * @param bool $isReg
     * @return $this
     */
    public function setErrorRegister($isReg = true)
    {
        Env::set('error_register', $isReg);
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
        if (!Env::get('error_register'))
        {
            throw new Exception('set setErrorRegister as true before use this api');
        }
        if (!$notify instanceof Closure && !is_string($notify))
        {
            throw new Exception('notify parameter can only be string or closure');
        }
        Env::set('error_register_notify', $notify);
        return $this;
    }

    /**
     * 新增匿名函数作为任务
     * @param Closure $func 匿名函数
     * @param string $alas 任务别名
     * @param mixed $time 定时器间隔
     * @param bool $persistent 持续执行
     * @param bool $push 是否投递任务
     * @return int 返回定时器Id
     * @throws
     */
    public function addTask($func, $alas, $time = 1, $persistent = true, $push = false)
    {
        if (!($func instanceof Closure))
        {
            throw new Exception('the func parameter must be a closure function');
        }
        return Helper::addTask([
            'type' => 1,
            'func' => $func,
            'alas' => $alas,
            'time' => $time,
            'persistent' => $persistent
        ], $push);
    }

    /**
     * 获取进程管理实例
     * @return  Win | Linux
     */
    private function getProcess()
    {
        if (Helper::isWin())
        {
            return (new Win());
        }
        else
        {
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