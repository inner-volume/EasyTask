<?php
namespace EasyTask;

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
     *  解析运行环境
     */
    public static function analysis()
    {
        //检查扩展
        $currentOs = Helper::isWin() ? 1 : 2;
        $waitExtends = static::$extends[$currentOs];
        foreach ($waitExtends as $extend) {
            if (!extension_loaded($extend)){
                Helper::showSysError("php_{$extend}.(dll/so) is not load,please check php.ini file");
            }
        }
        //检查函数
        $waitFunctions = static::$functions[$currentOs];
        foreach ($waitFunctions as $func) {
            if (!function_exists($func)){
                Helper::showSysError("function $func may be disabled,please check disable_functions in php.ini");
            }
        }
    }
}

