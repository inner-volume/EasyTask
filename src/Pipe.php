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
}
