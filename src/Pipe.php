<?php
namespace EasyTask;

use EasyTask\Helper\FileHelper;
use Exception;

/**
 * Class Pipe
 * @package EasyTask
 */
class Pipe
{
    /**
     * 命名管道文件
     * @var string
     */
    private $file;

    /**
     * 构造函数
     * @param string $name
     * @throws Exception
     */
    public function __construct($name = 'pipe')
    {
        $path = FileHelper::getPiePath();
        $this->file = $path . md5($name);
        if (!file_exists($this->file))
        {
            $error = "make pipe file:{$this->file} failed";
            if (!Helper::isWin())
            {
                if (!posix_mkfifo($this->file, 0666)) throw new Exception($error);
            }
            else
            {
                if (file_put_contents($this->file, '')) throw new Exception($error);
            }
        }
    }

    /**
     * 读取管道
     * @param int $size
     * @return string|false
     * @throws Exception
     */
    public function read($size = 0)
    {
        if ($size === 0)
        {
            $size = Helper::isWin() ? filesize($this->file) : $size = 4096;
        }
        $fileHand = fopen($this->file, 'r');
        if (!$fileHand)
        {
            throw new Exception("open pipe file:{$this->file} failed");
        }
        return fread($fileHand, $size);
    }

    /**
     * 写入管道
     * @param string $text
     * @return int|false
     * @throws Exception
     */
    public function write($text = '')
    {
        $allow_size = 4096;
        if (!Helper::isWin() && strlen($text) > 4096)
        {
            throw new Exception("The text size must not be greater than $allow_size");
        }
        $fileHand = fopen($this->file, 'a+');
        if (!$fileHand)
        {
            throw new Exception("open pipe file:{$this->file} failed");
        }
        return fwrite($fileHand, $text);
    }
}
