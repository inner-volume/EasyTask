<?php
namespace EasyTask;

use Exception;

/**
 * Class Check
 * @package EasyTask
 */
class Check
{
    /**
     * 检查扩展
     * @var array
     */
    private static $extends = [['json', 'curl', 'mbstring'], //common
        [], //Win
        ['pcntl', 'posix']];//Linux

    /**
     * 检查函数
     * @var array
     */
    private static $functions = [//Win
        ['umask', 'sleep', 'usleep', 'ob_start', 'ob_end_clean', 'ob_get_contents'], [], //Linux
        '2' => ['pcntl_fork', 'posix_setsid', 'posix_getpid', 'posix_getppid', 'pcntl_wait', 'posix_kill', 'pcntl_signal', 'pcntl_alarm', 'pcntl_waitpid', 'pcntl_signal_dispatch']];

    /**
     *  检查环境
     * @throws Exception
     */
    public static function analysis()
    {
        //检查扩展
        $currentOs = Helper::isWin() ? 1 : 2;
        $extends = static::$extends[$currentOs];
        $extends = array_merge($extends, static::$extends['0']);
        foreach ($extends as $extend) {
            if (!extension_loaded($extend)){
                throw new Exception("php_{$extend}.(dll/so) is not load,please check php.ini file");
            }
        }
        //检查函数
        $functions = static::$functions[$currentOs];
        $functions = array_merge($functions, static::$functions['0']);
        foreach ($functions as $func) {
            if (!function_exists($func)){
                throw new Exception("function $func may be disabled,please check disable_functions in php.ini");
            }
        }
    }
}

