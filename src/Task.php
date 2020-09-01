<?php
namespace EasyTask;

use \Closure as Closure;
use EasyTask\Helper\FileHelper;
use EasyTask\Helper\TimerHelper;
use EasyTask\Process\Process;
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
     * 构造函数
     * @throws Exception
     */
    public function __construct()
    {
        Check::analysis();
        $this->initialise();
    }

    /**
     * 进程初始化
     */
    private function initialise()
    {
        //初始化基础配置
        Env::set('prefix', 'Task');
        $this->setMode();
        $this->setQueueConfig();
        $this->setErrorRegister();

        //初始化PHP_BIN|CODE_PAGE
        if (Helper::isWin())
        {
            Helper::setPhpPath();
            Helper::setCodePage();
        }
    }

    /**
     * 设置运行模式
     * @param int $mode 1.同步 2.异步
     * @return $this
     */
    public function setMode($mode = 1)
    {
        Env::set('mode', $mode);
        return $this;
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
     * 设置任务前缀
     * @param string $prefix
     * @return $this
     * @throws Exception
     */
    public function setPrefix($prefix = 'Task')
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
     */
    public function setTimeZone($timeIdent)
    {
        date_default_timezone_set($timeIdent);
        return $this;
    }

    /**
     * 设置关闭标准输出
     * @param bool $close
     * @return $this
     */
    public function setCloseStdOut($close = false)
    {
        Env::set('close_std_out', $close);
        return $this;
    }

    /**
     * 设置运行时目录
     * @param string $path
     * @return $this
     * @throws Exception
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
        Env::set('runTimePath', realpath($path));
        FileHelper::initAllPath();
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
     * 设置异常回调
     * @param string|Closure $notify
     * @return $this
     * @throws Exception
     */
    public function setErrorRegisterNotify($notify)
    {
        if (Env::get('error_register'))
        {
            throw new Exception('you must set setErrorRegister as true before use this api');
        }
        if (!$notify instanceof Closure && !is_string($notify))
        {
            throw new Exception('notify parameter can only be string or closure');
        }
        Env::set('error_register_notify', $notify);
        return $this;
    }

    /**
     * 新增任务
     * @param string $class 类名称
     * @param string $func 方法名称
     * @param string $alas 任务别名
     * @param mixed $time 定时器间隔
     * @param bool $persistent 持续执行
     * @param bool $bySocket 是否通过Socket投递任务
     * @return int
     * @throws Exception
     */
    public function addClass($class, $func, $alas, $time = 1, $persistent = true, $bySocket = false)
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
            $task = [
                'type' => 1,
                'func' => $func,
                'alas' => $alas,
                'time' => $time,
                'persistent' => $persistent
            ];
            Timer::add($task, $bySocket);
        }
        catch (ReflectionException $exception)
        {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * 移除任务
     * @param string $timerId 定时器Id
     * @return bool
     * @throws Exception
     */
    public function removeTask($timerId)
    {
        return TimerHelper::removeTask($timerId);
    }

    /**
     * 清空任务
     * @param bool $exit 清空完成退出
     * @return bool
     * @throws Exception
     */
    public function clearTask($exit = false)
    {
        return TimerHelper::clearTask($exit);
    }

    /**
     * 开始运行
     * @throws Exception
     */
    public function start()
    {
        //目录构建
        FileHelper::initAllPath();

        //启动任务
        (new Process())->start();
    }

    /**
     * 运行状态
     * @throws
     */
    public function status()
    {
        (new Process())->status();
    }

    /**
     * 停止运行
     * @param bool $force 是否强制
     * @throws
     */
    public function stop($force = false)
    {
        (new Process())->stop($force);
    }
}