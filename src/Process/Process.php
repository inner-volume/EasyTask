<?php
namespace EasyTask\Process;

use EasyTask\Command;
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
class Process
{


    /**
     * 停止运行
     * @param bool $force 是否强制
     */
    public function stop($force = false)
    {

    }

    /**
     * 初始化任务数量
     */
    protected function setTaskCount()
    {
        $count = 0;
        foreach ($this->taskList as $key => $item)
        {
            $count += (int)$item['used'];
        }
        $this->taskCount = $count;
    }

    /**
     * 检查是否可写标准输出日志
     * @return bool
     */
    protected function canWriteStd()
    {
        return Env::get('daemon') && !Env::get('closeStdOutLog');
    }

    /**
     * 执行任务代码
     * @param array $item
     * @throws
     */
    protected function execute($item)
    {
        //根据任务类型执行
        $daemon = Env::get('daemon');

        //Std_Start
        if ($this->canWriteStd()) ob_start();
        try
        {
            $type = $item['type'];
            switch ($type)
            {
                case 1:
                    $func = $item['func'];
                    $func();
                    break;
                case 2:
                    call_user_func([$item['class'], $item['func']]);
                    break;
                case 3:
                    $object = new $item['class']();
                    call_user_func([$object, $item['func']]);
                    break;
                default:
                    @pclose(@popen($item['command'], 'r'));
            }

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
                Helper::writeLog(Helper::formatException($exception));
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
                Helper::writeLog(Helper::formatException($exception));
            }
        }

        //Std_End
        if ($this->canWriteStd())
        {
            $stdChar = ob_get_contents();
            if ($stdChar) Helper::saveStdChar($stdChar);
            ob_end_clean();
        }

        //检查常驻进程存活
        $this->checkDaemonForExit($item);
    }

    /**
     * 执行任务
     * @param array $item
     * @throws Throwable
     */
    protected function executeInvoker($item)
    {
        if ($item['time'] === 0)
        {
            $this->invokerByDirect($item);
        }
        else
        {
            Env::get('canEvent') ? $this->invokeByEvent($item) : $this->invokeByDefault($item);
        }
    }

    /**
     * 通过Event事件执行
     * @param array $item
     */
    protected function invokeByEvent($item)
    {
        //创建Event事件
        $eventConfig = new EventConfig();
        $eventBase = new EventBase($eventConfig);
        $event = new Event($eventBase, -1, Event::TIMEOUT | Event::PERSIST, function () use ($item) {
            try
            {
                $this->execute($item);
            }
            catch (Throwable $exception)
            {
                $type = 'exception';
                Error::report($type, $exception);
                $this->checkDaemonForExit($item);
            }
        });

        //添加事件
        $event->add($item['time']);

        //事件循环
        $eventBase->loop();
    }

    /**
     * 普通执行
     * @param array $item
     * @throws Throwable
     */
    protected function invokerByDirect($item)
    {
        $this->execute($item);
        exit;
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