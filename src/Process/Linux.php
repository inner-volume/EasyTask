<?php
namespace EasyTask\Process;

use EasyTask\Env;
use EasyTask\Helper;
use \Closure as Closure;
use EasyTask\Queue;
use EasyTask\Socket\Server;
use EasyTask\Task;
use Exception;
use \Throwable as Throwable;

/**
 * Class Linux
 * @package EasyTask\Process
 */
class Linux extends Process
{
    /**
     * 进程执行记录
     * @var array
     */
    protected $processList = [];

    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
        if (Helper::canUseAsyncSignal()) Helper::openAsyncSignal();
    }

    /**
     * 开始运行
     * @throws Exception
     */
    public function start()
    {
        //异步处理
        if (Env::get('daemon'))
        {
            Helper::setMask();
            $this->fork(
                function () {
                    $sid = posix_setsid();
                    if ($sid < 0)
                    {
                        throw new Exception('set child processForManager failed,please try again');
                    }
                    $this->manager();
                },
                function () {
                    pcntl_wait($status, WNOHANG);
                    $this->status();
                }
            );
        }

        //同步处理
        $this->manager();
    }

    /**
     * 分配任务
     * @param array $timer
     * @throws Throwable
     */
    protected function allocate($timer = [])
    {
        $timers = $timer ? [$timer] : Task::get();
        foreach ($timers as $timerId => $timer)
        {
            //分配进程
            $this->forkTimerToInvoker($timerId, $timer);
        }
    }

    /**
     * 创建子进程
     * @param Closure $childInvoke
     * @param Closure $mainInvoke
     */
    protected function fork($childInvoke, $mainInvoke)
    {
        $pid = pcntl_fork();
        if ($pid == -1)
        {
            Helper::showError('fork child process failed,please try again');
        }
        elseif ($pid)
        {
            $mainInvoke($pid);
        }
        else
        {
            $childInvoke();
        }
    }

    /**
     * 创建任务执行的子进程
     * @param int $timerId
     * @param array $timer
     * @throws Throwable
     */
    protected function forkTimerToInvoker($timerId, $timer)
    {
        $this->fork(
            function () use ($timer) {
                $this->timerInvoker($timer);
            },
            function ($pid) use ($timerId, $timer) {
                Task::change($timerId, 'pid', $pid);
                //set not block
                pcntl_wait($status, WNOHANG);
            }
        );
    }

    /**
     * 定时执行器
     * @param array $timer
     * @throws Throwable
     */
    protected function timerInvoker($timer)
    {
        //输出信息
        $item['ppid'] = posix_getppid();
        $text = "this worker {$timer['alas']}";
        Helper::writeTypeLog("$text is start");

        //进程标题
        Helper::cli_set_process_title($timer['alas']);

        //Kill信号
        pcntl_signal(SIGTERM, function () use ($text) {
            Helper::writeTypeLog("listened kill command, $text not to exit the program for safety");
        });

        //执行任务
        $this->executeInvoker($timer);
    }

    /**
     * 通过闹钟信号执行
     * @param array $item
     */
    protected function invokeByDefault($item)
    {
        //安装信号管理
        pcntl_signal(SIGALRM, function () use ($item) {
            pcntl_alarm($item['time']);
            $this->execute($item);
        }, false);

        //发送闹钟信号
        pcntl_alarm($item['time']);

        //挂起进程(同步调用信号,异步CPU休息)
        while (true)
        {
            //CPU休息
            Helper::sleep(1);

            //信号处理(同步/异步)
            if (!Env::get('canAsync')) pcntl_signal_dispatch();
        }
    }

    /**
     * 检查常驻进程是否存活
     * @param array $item
     */
    protected function checkDaemonForExit($item)
    {
        if (!posix_kill($item['ppid'], 0))
        {
            Helper::writeTypeLog("listened exit command, this worker {$item['alas']} is exiting safely", 'info', true);
        }
    }

    /**
     * 守护进程
     * @throws Exception|Throwable
     */
    protected function manager()
    {
        //任务分配
        $this->allocate();

        //进程标题
        Helper::cli_set_process_title(Env::get('prefix'));

        //输出信息
        $text = "this manager";
        Helper::writeTypeLog("$text is start");
        if (!Env::get('daemon'))
        {
            Helper::showTable($this->processStatus(), false);
            Helper::showInfo('start success,press ctrl+c to stop');
        }

        //注册信号
        pcntl_signal(SIGTERM, function () use ($text) {
            Helper::writeTypeLog("listened kill command $text is exiting safely", 'info', true);
        });

        //挂起进程
        $exitText = "listened exit command, $text is exiting safely";
        $statusText = "listened status command, $text is reported";
        $forceExitText = "listened exit command, $text is exiting unsafely";
        $client_queue = new Queue('master');
        $server_queue = new Queue('manage');
        while (true)
        {
            $command = $server_queue->shift();
            if ($command)
            {
                //提取参数
                $cid = $command['cid'];
                $action = $command['action'];
                $response = $command['response'];
                if ($action == 'start')
                {
                    Helper::writeTypeLog($forceExitText);
                    posix_kill(0, SIGKILL);
                }
                if ($action == 'status')
                {
                    $status = $this->processStatus();
                    $client_queue->push([
                        'id' => $cid,
                        'action' => $action,
                        'response' => $status
                    ]);
                }
                if ($action == 'stop')
                {
                    if ($response['force'])
                    {
                        Helper::writeTypeLog($forceExitText);
                        posix_kill(0, SIGKILL);
                    }
                    else
                    {
                        Helper::writeTypeLog($exitText);
                        exit();
                    }
                }
                if ($action == 'addTask')
                {
                    $timerId = Task::set($response);
                    $this->allocate($response);
                    $client_queue->push([
                        'id' => $cid,
                        'action' => $action,
                        'response' => $timerId
                    ]);
                }
            }

            //信号调度
            if (!Helper::canUseAsyncSignal()) pcntl_signal_dispatch();

            //检查进程(考虑间隔检查)
            if (Env::get('auto_recover')) $this->processStatus();
        }
    }

    /**
     * 守护进程-客户端消息处理
     * @param string $message 客户端消息
     */
    protected function managerOnMessage($message)
    {
        $json = base64_decode($message);
        if (!$json)
        {
            Helper::writeTypeLog("client data base64 parsing exception:$message");
        }
        $data = json_decode($json, true);
        if (!$data)
        {
            Helper::writeTypeLog("client data json parsing exception:$json");
        }
        $command = $data;

        $text = '';
        $exitText = "listened exit command, $text is exiting safely";
        $statusText = "listened status command, $text is reported";
        $forceExitText = "listened exit command, $text is exiting unsafely";
        if ($command['action'] == 'start')
        {
            if ($command['time'] > $this->startTime)
            {
                Helper::writeTypeLog($forceExitText);
                posix_kill(0, SIGKILL);
            }
        }
        if ($command['action'] == 'status')
        {
            $report = $this->processStatus();
            $this->commander->send([
                'type' => 'status',
                'msgType' => 1,
                'status' => $report,
            ]);
            Helper::writeTypeLog($statusText);
        }
        if ($command['action'] == 'stop')
        {
            if ($command['force'])
            {
                Helper::writeTypeLog($forceExitText);
                posix_kill(0, SIGKILL);
            }
            else
            {
                Helper::writeTypeLog($exitText);
                exit();
            }
        }
    }

    /**
     * 守护进程-事件循环
     */
    protected function managerOnEventLoop()
    {
        //信号调度
        if (Helper::canUseAsyncSignal()) pcntl_signal_dispatch();

        //检查进程
        if (Env::get('auto_recover')) $this->processStatus();
    }

    /**
     * 查看进程状态
     * @return array
     */
    protected function processStatus()
    {
        $report = [];
        foreach ($this->processList as $key => $item)
        {
            //提取参数
            $pid = $item['pid'];

            //进程状态
            $rel = pcntl_waitpid($pid, $status, WNOHANG);
            if ($rel == -1 || $rel > 0)
            {
                //标记状态
                $item['status'] = 'stop';

                //进程退出,重新fork
                if (Env::get('canAutoRec'))
                {
                    $this->forkItemExec($item['item']);
                    Helper::writeTypeLog("the worker {$item['name']}(pid:{$pid}) is stop,try to fork a new one");
                    unset($this->processList[$key]);
                }
            }

            //记录状态
            unset($item['item']);
            $report[] = $item;
        }

        return $report;
    }
}