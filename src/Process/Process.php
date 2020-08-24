<?php
namespace EasyTask\Process;

use EasyTask\Wts;
use EasyTask\Wpc;
use EasyTask\Env;
use EasyTask\Helper;
use \Exception as Exception;
use \Throwable as Throwable;

/**
 * Class Win
 * @package EasyTask\Process
 */
class Process
{
    /**
     * 开始运行的检查
     * @throws Exception
     */
    public function checkForRun()
    {
        //Win32检查PHP二进制目录
        if (!Env::get('phpPath') && Helper::isWin())
        {
            throw new Exception('please use setPhpPath api to set phpPath');
        }

        //检查Manager进程
    }
}