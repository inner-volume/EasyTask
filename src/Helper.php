<?php
namespace EasyTask;

use EasyTask\Cron\CronExpression;
use EasyTask\Exception\ErrorException;
use \Exception as Exception;

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
     * @param $title
     */
    public static function cli_set_process_title($title)
    {
        if (function_exists('cli_set_process_title'))
        {
            @cli_set_process_title($title);
        }
    }

    /**
     * 设置代码页
     * @param int $code
     */
    public static function setCodePage($code = 65001)
    {
        if (static::canExecuteCommand())
        {
            @pclose(@popen("chcp {$code}", 'r'));
        }
    }

    /**
     * 二维数组转字典
     * @param array $list
     * @param string $key
     * @return array
     */
    public static function array_dict($list, $key)
    {
        $dict = [];
        foreach ($list as $v)
        {
            if (!isset($v[$key]))
            {
                continue;
            }
            $dict[$v[$key]] = $v;
        }

        return $dict;
    }

    /**
     * 获取命令行输入
     * @param $type
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
     * 获取PHP二进制文件
     * @return string
     */
    public static function getPhpPath()
    {
        $file = dirname(php_ini_loaded_file()) . DIRECTORY_SEPARATOR . 'php';
        if (Helper::isWin())
        {
            $file .= '.exe';
        }
        return file_exists($file) ? $file : '';
    }

    /**
     * 设置PHP二进制文件
     * @param $path
     */
    public static function setPhpPath($path = '')
    {
        if (!$path) $path = static::getPhpPath();
        Env::set('phpPath', $path);
    }

    /**
     * 是否Win平台
     * @return bool
     */
    public static function isWin()
    {
        return (DIRECTORY_SEPARATOR == '\\') ? true : false;
    }

    /**
     * 是否可执行命令
     * @return bool
     */
    public static function canExecuteCommand()
    {
        return function_exists('popen') && function_exists('pclose');
    }

    /**
     * 获取运行时目录
     * @return  string
     */
    public static function getRunTimePath()
    {
        $path = Env::get('runTimePath');
        if (!$path)
        {
            static::showSysError('please invoke setRunTimePath function to set runPath');
        }
        $path = $path . DIRECTORY_SEPARATOR . Env::get('prefix') . DIRECTORY_SEPARATOR;
        $path = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $path);
        return $path;
    }

    /**
     * 获取Win进程目录
     * @return  string
     */
    public static function getWinPath()
    {
        return Helper::getRunTimePath() . 'Win' . DIRECTORY_SEPARATOR;
    }

    /**
     * 获取日志目录
     * @return  string
     */
    public static function getLogPath()
    {
        return Helper::getRunTimePath() . 'Log' . DIRECTORY_SEPARATOR;
    }

    /**
     * 获取进程命令通信目录
     * @return  string
     */
    public static function getCsgPath()
    {
        return Helper::getRunTimePath() . 'Csg' . DIRECTORY_SEPARATOR;
    }

    /**
     * 获取标准输入输出目录
     * @return  string
     */
    public static function getStdPath()
    {
        return Helper::getRunTimePath() . 'Std' . DIRECTORY_SEPARATOR;
    }

    /**
     * 初始化所有目录
     */
    public static function initAllPath()
    {
        $paths = [
            static::getRunTimePath(),
            static::getWinPath(),
            static::getLogPath(),
            static::getCsgPath(),
            static::getStdPath(),
        ];
        foreach ($paths as $path)
        {
            if (!is_dir($path))
            {
                mkdir($path, 0777, true);
            }
        }
    }

    /**
     * 关闭标准Std
     */
    public static function setStdClose()
    {
        global $STDOUT, $STDERR;
        $path = static::getStdPath();
        $file = $path . date('Y_m_d') . '.txt';
        $handle = fopen($file, "a");
        if ($handle)
        {
            unset($handle);
            @fclose(STDOUT);
            @fclose(STDERR);
            $STDOUT = fopen($file, "a");
            $STDERR = fopen($file, "a");
        }
        else
        {
            static::showError("std file {$file} can not open");
        }
    }

    /**
     * 保存标准输入|输出
     * @param string $char 输入|输出
     */
    public static function saveStdChar($char)
    {
        $path = static::getStdPath();
        $file = $path . date('Y_m_d') . '.txt';
        $char = static::convert_char($char);
        file_put_contents($file, $char . PHP_EOL, FILE_APPEND);
    }

    /**
     * 保存日志
     * @param string $message
     */
    public static function writeLog($message)
    {
        //日志文件
        $path = Helper::getLogPath();
        $file = $path . date('Y_m_d') . '.txt';

        //加锁保存
        $message = static::convert_char($message);
        file_put_contents($file, $message, FILE_APPEND | LOCK_EX);
    }

    /**
     * 保存类型日志
     * @param $message
     * @param string $type
     * @param bool $isExit
     */
    public static function writeTypeLog($message, $type = 'info', $isExit = false)
    {
        //格式化信息
        $text = Helper::formatMessage($message, $type);

        //记录日志
        static::writeLog($text);
        if ($isExit) exit();
    }

    /**
     * 是否支持异步信号
     * @return bool
     */
    public static function canAsyncSignal()
    {
        return (function_exists('pcntl_async_signals'));
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
     * 是否支持event事件
     * @return bool
     */
    public static function canEvent()
    {
        return (extension_loaded('event'));
    }

    /**
     * 编码转换
     * @param string $char
     * @param string $coding
     * @return string
     */
    public static function convert_char($char, $coding = 'UTF-8')
    {
        $encode_arr = [
            'UTF-8',
            'ASCII',
            'GBK',
            'GB2312',
            'BIG5',
            'JIS',
            'eucjp-win',
            'sjis-win',
            'EUC-JP'
        ];
        $encoded = mb_detect_encoding($char, $encode_arr);
        return mb_convert_encoding($char, $coding, $encoded);
    }

    /**
     * 格式化异常信息
     * @param ErrorException $exception
     * @param string $type
     * @return string
     */
    public static function formatException($exception, $type = 'exception')
    {
        //时间
        $date = date('Y/m/d H:i:s', time());

        //组装文本
        return $date . ' [' . $type . '] : errStr:' . $exception->getMessage() . ',errFile:' . $exception->getFile() . ',errLine:' . $exception->getLine() . PHP_EOL;
    }

    /**
     * 格式化异常信息
     * @param string $message
     * @param string $type
     * @return string
     */
    public static function formatMessage($message, $type = 'error')
    {
        //时间
        $date = date('Y/m/d H:i:s', time());

        //组装文本
        return $date . ' [' . $type . '] : ' . $message . PHP_EOL;
    }

    /**
     * 检查任务时间是否合法
     * @param $time
     */
    public static function checkTaskTime($time)
    {
        if (is_int($time))
        {
            if ($time < 0) static::showSysError('time must be greater than or equal to 0');
        }
        elseif (is_float($time))
        {
            if (!static::canEvent()) static::showSysError('please install php_event.(dll/so) extend for using milliseconds');
        }
        elseif (is_string($time))
        {
            if (!CronExpression::isValidExpression($time))
            {
                static::showSysError("$time is not a valid CRON expression");
            }
        }
        else
        {
            static::showSysError('time parameter is an unsupported type');
        }
    }

    /**
     * 获取Cron命令的下次执行时间
     * @param string $command cron命令
     * @param string $currentTime cron命令
     * @return string
     */
    public static function getCronNextDate($command, $currentTime = 'now')
    {
        static $cronExpression = null;
        $nextDate = null;
        if (!$cronExpression) $cronExpression = CronExpression::factory($command);
        try
        {
            $nextDate = $cronExpression->getNextRunDate($currentTime)->format('Y-m-d H:i:s');
        }
        catch (Exception $exception)
        {
            Helper::showSysError($exception->getMessage());
        }
        return $nextDate;
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