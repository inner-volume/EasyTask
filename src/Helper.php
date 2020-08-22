<?php
namespace EasyTask;

use EasyTask\Exception\ErrorException;
use \Exception as Exception;
use \Throwable as Throwable;

/**
 * Class Helper
 * @package EasyTask
 */
class Helper
{
    /**
     * 睡眠函数
     * @param int $time 时间
     * @param int $type 类型:1秒 2毫秒
     */
    public static function sleep($time, $type = 1)
    {
        if ($type == 2) $time *= 1000;
        $type == 1 ? sleep($time) : usleep($time);
    }

    /**
     * 设置进程标题
     * @param string $title
     */
    public static function cli_set_process_title($title)
    {
        set_error_handler(function () {
        });
        if (function_exists('cli_set_process_title'))
        {
            cli_set_process_title($title);
        }
        restore_error_handler();
    }

    /**
     * 设置掩码
     */
    public static function setMask()
    {
        umask(0);
    }

    /**
     * 设置代码页
     * @param int $code
     */
    public static function setCodePage($code = 65001)
    {
        $ds = DIRECTORY_SEPARATOR;
        $codePageBinary = "C:{$ds}Windows{$ds}System32{$ds}chcp.com";
        if (file_exists($codePageBinary) && static::canUseExcCommand())
        {
            @pclose(@popen("{$codePageBinary} {$code}", 'r'));
        }
    }

    /**
     * 获取命令行输入
     * @param int $type
     * @return string|array
     */
    public static function getCliInput($type = 1)
    {
        //输入参数
        $argv = $_SERVER['argv'];

        //组装PHP路径
        array_unshift($argv, Env::get('phpPath'));

        //自动校正
        foreach ($argv as $key => $value)
        {
            if (file_exists($value))
            {
                $argv[$key] = realpath($value);
            }
        }

        //返回
        if ($type == 1)
        {
            return join(' ', $argv);
        }
        return $argv;
    }

    /**
     * 设置PHP二进制文件
     * @param string $path
     */
    public static function setPhpPath($path = '')
    {
        if (!$path) $path = PHP_BINARY;
        Env::set('phpPath', $path);
    }

    /**
     * 是否Win平台
     * @return bool
     */
    public static function isWin()
    {
        return DIRECTORY_SEPARATOR == '\\';
    }

    /**
     * 开启异步信号
     * @return bool
     */
    public static function openAsyncSignal()
    {
        return pcntl_async_signals(true);
    }

    /**
     * 是否支持异步信号
     * @return bool
     */
    public static function canUseAsyncSignal()
    {
        return (function_exists('pcntl_async_signals'));
    }

    /**
     * 是否支持event事件
     * @return bool
     */
    public static function canUseEvent()
    {
        return (extension_loaded('event'));
    }

    /**
     * 是否可执行命令
     * @return bool
     */
    public static function canUseExcCommand()
    {
        return function_exists('popen') && function_exists('pclose');
    }



    /**
     * 输出字符串
     * @param string $char
     * @param bool $exit
     */
    public static function output($char, $exit = false)
    {
        echo $char;
        if ($exit) exit();
    }

    /**
     * 输出信息
     * @param string $message
     * @param bool $isExit
     * @param string $type
     * @throws
     */
    public static function showInfo($message, $isExit = false, $type = 'info')
    {
        //格式化信息
        $text = static::formatMessage($message, $type);

        //记录日志
        static::writeLog($text);

        //输出信息
        static::output($text, $isExit);
    }

    /**
     * 输出错误
     * @param string $errStr
     * @param bool $isExit
     * @param string $type
     * @param bool $log
     * @throws
     */
    public static function showError($errStr, $isExit = true, $type = 'error', $log = true)
    {
        //格式化信息
        $text = static::formatMessage($errStr, $type);

        //记录日志
        if ($log) static::writeLog($text);

        //输出信息
        static::output($text, $isExit);
    }

    /**
     * 输出系统错误
     * @param string $errStr
     * @param bool $isExit
     * @param string $type
     * @throws
     */
    public static function showSysError($errStr, $isExit = true, $type = 'warring')
    {
        //格式化信息
        $text = static::formatMessage($errStr, $type);

        //输出信息
        static::output($text, $isExit);
    }

    /**
     * 输出异常
     * @param mixed $exception
     * @param string $type
     * @param bool $isExit
     * @throws
     */
    public static function showException($exception, $type = 'exception', $isExit = true)
    {
        //格式化信息
        $text = static::formatException($exception, $type);

        //记录日志
        Helper::writeLog($text);

        //输出信息
        static::output($text, $isExit);
    }

    /**
     * 控制台输出表格
     * @param array $data
     * @param boolean $exit
     */
    public static function showTable($data, $exit = true)
    {
        //提取表头
        $header = array_keys($data['0']);

        //组装数据
        foreach ($data as $key => $row)
        {
            $data[$key] = array_values($row);
        }

        //输出表格
        $table = new Table();
        $table->setHeader($header);
        $table->setStyle('box');
        $table->setRows($data);
        $render = static::convert_char($table->render());
        if ($exit)
        {
            exit($render);
        }
        echo($render);
    }

    /**
     * 通过Curl方式提交数据
     *
     * @param string $url 目标URL
     * @param null $data 提交的数据
     * @param bool $return_array 是否转成数组
     * @param null $header 请求头信息 如：array("Content-Type: application/json")
     *
     * @return array|mixed
     */
    public static function curl($url, $data = null, $return_array = false, $header = null)
    {
        //初始化curl
        $curl = curl_init();

        //设置超时
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        if (is_array($header))
        {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }
        if ($data)
        {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        //运行curl，获取结果
        $result = @curl_exec($curl);

        //关闭句柄
        curl_close($curl);

        //转成数组
        if ($return_array)
        {
            return json_decode($result, true);
        }

        //返回结果
        return $result;
    }
}