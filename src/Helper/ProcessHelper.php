<?php
namespace EasyTask\Helper;

use EasyTask\Exception\ErrorException;
use EasyTask\Helper;
use EasyTask\Process\Linux;
use EasyTask\Process\Win;
use Exception;

/**
 * Class ProcessHelper
 * @package EasyTask
 */
class ProcessHelper
{
    /**
     * 获取环境变量
     * @param string $key
     * @return string
     */
    public static function getEnv($key)
    {
        return getenv($key);
    }

    /**
     * 获取进程实例
     * @return Win|Linux
     */
    public static function getInstance()
    {
        return Helper::isWin() ? new Win() : new Linux();
    }

    /**
     * 格式化异常信息
     * @param string $message
     * @param string $type
     * @return string
     */
    public static function formatMessage($message, $type = 'error')
    {
        //参数
        $pid = getmypid();
        $date = date('Y/m/d H:i:s', time());

        //组装
        return $date . " [$type] : " . $message . " (pid:$pid)" . PHP_EOL;
    }
}