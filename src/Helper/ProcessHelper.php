<?php
namespace EasyTask\Helper;

use EasyTask\Exception\ErrorException;
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
     * 格式化异常信息
     * @param ErrorException|Exception
     * @param string $type
     * @return string
     */
    public static function formatException($exception, $type = 'exception')
    {
        //参数
        $pid = getmypid();
        $date = date('Y/m/d H:i:s', time());

        //组装
        return $date . " [$type] : errStr:" . $exception->getMessage() . ',errFile:' . $exception->getFile() . ',errLine:' . $exception->getLine() . " (pid:$pid)" . PHP_EOL;
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