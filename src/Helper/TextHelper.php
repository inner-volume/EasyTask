<?php
namespace EasyTask\Helper;
use EasyTask\Exception\ErrorException;
use Exception;

/**
 * Class TextHelper
 * @package EasyTask
 */
class TextHelper
{
    /**
     * 编码转换
     * @param string $char
     * @param string $coding
     * @return string
     */
    public static function convert_char($char, $coding = 'UTF-8')
    {
        $encode_arr = ['UTF-8', 'ASCII', 'GBK', 'GB2312', 'BIG5', 'JIS', 'eucjp-win', 'sjis-win', 'EUC-JP'];
        $encoded = mb_detect_encoding($char, $encode_arr);
        if ($encoded)
        {
            $char = mb_convert_encoding($char, $coding, $encoded);
        }
        return $char;
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