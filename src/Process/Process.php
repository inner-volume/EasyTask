<?php
namespace EasyTask\Process;

use EasyTask\Env;
use EasyTask\Error;
use EasyTask\Helper;
use EasyTask\Timer;
use \Exception as Exception;
use \Throwable as Throwable;

/**
 * Class Process
 * @package EasyTask\Process
 */
abstract class Process
{
    /**
     * 任务列表
     * @var array
     */
    protected $tasks;

    /**
     * 构造函数
     */
    public function __construct()
    {
        //$this->tasks[] = Timer::get();
    }

    /**
     * 开始运行
     */
    abstract public function start();

    /**
     * 运行状态
     */
    public function status()
    {
        $this->masterWaitExit();
    }

    /**
     * 停止运行
     * @param bool $force 是否强制
     */
    public function stop($force = false)
    {
    }

    /**
     * 执行任务代码
     * @param array $item
     * @throws
     */
    protected function execute($item)
    {
        //Daemon
        $daemon = Env::get('daemon');

        //Std_Start
        $canWriteStdOut = Helper\ProcessHelper::canWriteStdOut();
        if ($canWriteStdOut) ob_start();
        try
        {
            //静态
            call_user_func([$item['class'], $item['func']]);

            //非静态
            $object = new $item['class']();
            call_user_func([$object, $item['func']]);

        }
        catch (Exception $exception)
        {
            if (Helper::isWin())
            {
                Helper::showException($exception, 'exception', !$daemon);
            }
            else
            {
                if (!$daemon) throw $exception;
                Helper\FileHelper::writeLog(Helper\TextHelper::formatException($exception));
            }
        }
        catch (Throwable $exception)
        {
            if (Helper::isWin())
            {
                Helper::showException($exception, 'exception', !$daemon);
            }
            else
            {
                if (!$daemon) throw $exception;
                Helper\FileHelper::writeLog(Helper\TextHelper::formatException($exception));
            }
        }

        //Std_End
        if ($canWriteStdOut)
        {
            $stdChar = ob_get_contents();
            if ($stdChar) Helper\FileHelper::saveStdChar($stdChar);
            ob_end_clean();
        }

        //检查Manage进程存活
        $this->checkDaemonForExit($item);
    }

    /**
     * 执行任务
     * @param array $item
     * @throws Throwable
     */
    protected function executeInvoker($item)
    {
        if ($item['time'] === 0 || Env::get('mode') === 2)
        {
            $this->execute($item);
            exit();
        }
        else
        {
            while (true)
            {
                Helper::sleep($item['time']);
                $this->execute($item);
            }
        }
    }

    /**
     * 主进程等待结束退出
     */
    protected function masterWaitExit()
    {
        $i = $this->taskCount + 30;
        while ($i--)
        {
            //接收汇报
            $this->commander->waitCommandForExecute(1, function ($report) {
                if ($report['type'] == 'status' && $report['status'])
                {
                    Helper::showTable($report['status']);
                }
            }, $this->startTime);

            //CPU休息
            Helper::sleep(1);
        }
        Helper::showInfo('this cpu is too busy,please use status command try again');
        exit;
    }
}