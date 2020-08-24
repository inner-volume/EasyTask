<?php
namespace EasyTask\Helper;

use EasyTask\Env;
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
     * 检查是否可写标准输出日志
     * @return bool
     */
    public static function canWriteStdOut()
    {
        return Env::get('daemon') && !Env::get('close_std_out');
    }
}