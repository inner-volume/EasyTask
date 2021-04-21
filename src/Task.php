<?php
namespace EasyTask;

use \Closure as Closure;
use EasyTask\Helper\UtilHelper;
use EasyTask\Process\Linux;
use EasyTask\Process\Win;
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
     * task list
     * @var array
     */
    private $taskList = [];

    /**
     * constructor
     */
    public function __construct()
    {
        //check the operating environment
        Check::analysis();

        //initialize the basic configuration
        $this->setPrefix(Constant::SERVER_PREFIX_VAL);
        $this->setCloseErrorRegister();
        if (UtilHelper::isWin())
        {
            Helper::setPhpPath();
            Helper::setCodePage();
        }
    }

    /**
     * setDaemon
     * @param bool $daemon
     * @return $this
     */
    public function setDaemon($daemon = false)
    {
        Env::set('daemon', $daemon);
        return $this;
    }

    /**
     * setPrefix
     * @param string $prefix
     * @return $this
     */
    public function setPrefix($prefix)
    {
        //runTimePath
        if (Env::get(Constant::SERVER_RUNTIME_PATH))
        {
            Helper::showSysError('should use setPrefix before setRunTimePath');
        }
        Env::set(Constant::SERVER_PREFIX_KEY, $prefix);
        return $this;
    }

    /**
     * setPhpPath
     * @param string $path
     * @return $this
     */
    public function setPhpPath($path)
    {
        $file = realpath($path);
        if (!file_exists($file))
        {
            Helper::showSysError("the path {$path} is not exists");
        }
        Helper::setPhpPath($path);
        return $this;
    }

    /**
     * setTimeZone
     * @param string $timeIdent
     * @return $this
     */
    public function setTimeZone($timeIdent)
    {
        date_default_timezone_set($timeIdent);
        return $this;
    }

    /**
     * setRunTimePath
     * @param string $path
     * @return $this
     */
    public function setRunTimePath($path)
    {
        if (!is_dir($path))
        {
            Helper::showSysError("the path {$path} is not exist");
        }
        if (!is_writable($path))
        {
            Helper::showSysError("the path {$path} is not writeable");
        }
        Env::set(Constant::SERVER_RUNTIME_PATH, realpath($path));
        return $this;
    }

    /**
     * setAutoRecover
     * @param bool $isRec
     * @return $this
     */
    public function setAutoRecover($isRec = false)
    {
        Env::set('canAutoRec', $isRec);
        return $this;
    }

    /**
     * setCloseStdOutLog
     * @param bool $close
     * @return $this
     */
    public function setCloseStdOutLog($close = false)
    {
        Env::set('closeStdOutLog', $close);
        return $this;
    }

    /**
     * setCloseErrorRegister
     * @param bool $close
     * @return $this
     */
    public function setCloseErrorRegister($close = false)
    {
        Env::set(Constant::SERVER_CLOSE_ERROR_REGISTER_SWITCH_KEY, $close);
        return $this;
    }

    /**
     * setErrorRegisterNotify
     * @param string|Closure $notify
     * @return $this
     */
    public function setErrorRegisterNotify($notify)
    {
        if (Env::get(Constant::SERVER_CLOSE_ERROR_REGISTER_SWITCH_KEY))
        {
            Helper::showSysError('you must set closeErrorRegister as false before use this api');
        }
        if (!$notify instanceof Closure && !is_string($notify))
        {
            Helper::showSysError('notify parameter can only be string or closure');
        }
        Env::set('notifyHand', $notify);
        return $this;
    }

    /**
     * addFunc
     * @param Closure $func
     * @param string $alas 任务别名
     * @param mixed $time 定时器间隔
     * @param int $used 定时器占用进程数
     * @return $this
     * @throws
     */
    public function addFunc($func, $alas, $time = 1, $used = 1)
    {
        $uniqueId = md5($alas);
        if (!($func instanceof Closure))
        {
            Helper::showSysError('func must instanceof Closure');
        }
        if (isset($this->taskList[$uniqueId]))
        {
            Helper::showSysError("task $alas already exists");
        }
        Helper::checkTaskTime($time);
        $this->taskList[$uniqueId] = [
            'type' => 1,
            'func' => $func,
            'alas' => $alas,
            'time' => $time,
            'used' => $used
        ];

        return $this;
    }

    /**
     * 新增类作为任务
     * @param string $class 类名称
     * @param string $func 方法名称
     * @param string $alas 任务别名
     * @param mixed $time 定时器间隔
     * @param int $used 定时器占用进程数
     * @return $this
     * @throws
     */
    public function addClass($class, $func, $alas, $time = 1, $used = 1)
    {
        $uniqueId = md5($alas);
        if (!class_exists($class))
        {
            Helper::showSysError("class {$class} is not exist");
        }
        if (isset($this->taskList[$uniqueId]))
        {
            Helper::showSysError("task $alas already exists");
        }
        try
        {
            $reflect = new ReflectionClass($class);
            if (!$reflect->hasMethod($func))
            {
                Helper::showSysError("class {$class}'s func {$func} is not exist");
            }
            $method = new ReflectionMethod($class, $func);
            if (!$method->isPublic())
            {
                Helper::showSysError("class {$class}'s func {$func} must public");
            }
            Helper::checkTaskTime($time);
            $this->taskList[$uniqueId] = [
                'type' => $method->isStatic() ? 2 : 3,
                'func' => $func,
                'alas' => $alas,
                'time' => $time,
                'used' => $used,
                'class' => $class
            ];
        }
        catch (ReflectionException $exception)
        {
            Helper::showException($exception);
        }

        return $this;
    }

    /**
     * 新增指令作为任务
     * @param string $command 指令
     * @param string $alas 任务别名
     * @param mixed $time 定时器间隔
     * @param int $used 定时器占用进程数
     * @return $this
     */
    public function addCommand($command, $alas, $time = 1, $used = 1)
    {
        $uniqueId = md5($alas);
        if (!Helper::canUseExcCommand())
        {
            Helper::showSysError('please open the disabled function of popen and pclose');
        }
        if (isset($this->taskList[$uniqueId]))
        {
            Helper::showSysError("task $alas already exists");
        }
        Helper::checkTaskTime($time);
        $this->taskList[$uniqueId] = [
            'type' => 4,
            'alas' => $alas,
            'time' => $time,
            'used' => $used,
            'command' => $command,
        ];

        return $this;
    }

    /**
     * getProcess
     * @return  Win | Linux
     */
    private function getProcess()
    {
        $taskList = $this->taskList;
        return UtilHelper::isWin() ? (new Win($taskList)) : (new Linux($taskList));
    }

    /**
     * 开始运行
     * @throws
     */
    public function start()
    {
        //task empty tip
        if (!$this->taskList) Helper::showSysError(Constant::SERVER_TASK_EMPTY_TIP);

        //exception registration
        if (!Env::get(Constant::SERVER_CLOSE_ERROR_REGISTER_SWITCH_KEY)) Error::register();

        //directory construction
        Helper::initAllPath();

        //process start
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