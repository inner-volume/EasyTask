<?php
namespace EasyTask\Process;

use Closure;
use Exception;

/**
 * Class Process
 * @package EasyTask\Process
 */
class Process
{
    /**
     * 创建unix子进程
     * @param Closure $childInvoke
     * @param Closure $mainInvoke
     * @throws
     */
    protected function unix_fork($childInvoke, $mainInvoke)
    {
        $pid = pcntl_fork();
        if ($pid == -1){
            throw new Exception('fork child process failed,please try again');
        }
        elseif ($pid){
            $mainInvoke($pid);
        }
        else{
            $childInvoke();
        }
    }

    /**
     * unix进程等待
     */
    protected function unix_wait()
    {
        pcntl_wait($status, WNOHANG);
    }

    /**
     * 设置unix进程leader
     * @throws Exception
     */
    protected function unix_setLeader()
    {
        $sid = posix_setsid();
        if ($sid < 0){
            throw new Exception('set child processForManager failed,please try again');
        }
    }
}