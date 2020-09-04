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
 * Class Task
 * @package EasyTask
 */
class Task
{
    /**
     * 任务列表
     * @var array
     */
    private $taskList = [];

    /**
     * 构造函数
     */
    public function __construct()
    {
        //检查运行环境
        $currentOs = Helper::isWin() ? 1 : 2;
        Check::analysis($currentOs);
        $this->initialise($currentOs);
    }

    /**
     * 进程初始化
     * @param int $currentOs
     */
    private function initialise($currentOs)
    {
        //初始化基础配置
        Env::set('prefix', 'Task');
        Env::set('canEvent', Helper::canUseEvent());
        Env::set('currentOs', $currentOs);
        Env::set('canAsync', Helper::canUseAsyncSignal());
        Env::set('error_register', true);

        //初始化PHP_BIN|CODE_PAGE
        if ($currentOs == 1)
        {
            Helper::setPhpPath();
            Helper::setCodePage();
        }
    }

    /**
     * 设置是否守护进程
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
     * 设置PHP执行路径|Windows
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
     * 设置子进程自动恢复
     * @param bool $recover
     * @return $this
     */
    public function setAutoRecover($recover = false)
    {
        Env::set('auto_recover', $recover);
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
    public function addFunc($func, $alas, $time = 1, $persistent = true, $push = false)
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
     * 新增类作为任务
     * @param string $class 类名称
     * @param string $func 方法名称
     * @param string $alas 任务别名
     * @param mixed $time 定时器间隔
     * @param bool $persistent 持续执行
     * @param bool $push 是否投递任务
     * @return int 返回定时器Id
     * @throws
     */
    public function addClass($class, $func, $alas, $time = 1, $persistent = true, $push = false)
    {
        if (!class_exists($class))
        {
            throw new Exception("class {$class} is not exist");
        }
        try
        {
            $reflect = new ReflectionClass($class);
            if (!$reflect->hasMethod($func))
            {
                throw new Exception("class {$class}'s func {$func} is not exist");
            }
            $method = new ReflectionMethod($class, $func);
            if (!$method->isPublic())
            {
                throw new Exception("class {$class}'s func {$func} must public");
            }
            return Helper::addTask([
                'type' => $method->isStatic() ? 2 : 3,
                'func' => $func,
                'alas' => $alas,
                'time' => $time,
                'class' => $class,
                'persistent' => $persistent
            ], $push);
        }
        catch (ReflectionException $exception)
        {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * 新增指令作为任务
     * @param string $command 指令
     * @param string $alas 任务别名
     * @param mixed $time 定时器间隔
     * @param bool $persistent 持续执行
     * @param bool $push 是否投递任务
     * @return int 返回定时器Id
     * @throws Exception
     */
    public function addCommand($command, $alas, $time = 1, $persistent = true, $push = false)
    {
        if (!Helper::canUseExcCommand())
        {
            throw new Exception('please open the disabled function of popen and pclose');
        }
        return Helper::addTask([
            'type' => 4,
            'alas' => $alas,
            'time' => $time,
            'command' => $command,
            'persistent' => $persistent
        ], $push);
    }

    /**
     * 获取进程管理实例
     * @return  Win | Linux
     */
    private function getProcess()
    {
        $taskList = $this->taskList;
        $currentOs = Env::get('currentOs');
        if ($currentOs == 1)
        {
            return (new Win($taskList));
        }
        else
        {
            return (new Linux($taskList));
        }
    }

    /**
     * 开始运行
     * @throws
     */
    public function start()
    {
        if (!$this->taskList)
        {
            Helper::showSysError('please add task to run');
        }

        //异常注册
        if (!Env::get('closeErrorRegister'))
        {
            Error::register();
        }

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