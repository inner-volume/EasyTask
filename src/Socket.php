<?php
namespace EasyTask;

use \Closure as Closure;
use EasyTask\Helper\FileHelper;
use Exception;

/**
 * Class Lock
 * @package EasyTask
 */
class Socket
{
    /**
     * 锁文件
     * @var string
     */
    private $file;

    /**
     * 构造函数
     * @param string $name
     * @throws Exception
     */
    public function __construct($name = 'lock')
    {
        //初始化文件
        $path = FileHelper::getLokPath();
        $this->file = $path . md5($name);
        if (!file_exists($this->file))
        {
            @file_put_contents($this->file, '');
        }
    }

    /**
     * 加锁执行
     * @param Closure $func
     * @param bool $block
     * @return mixed
     */
    public function execute($func, $block = true)
    {
        $fp = fopen($this->file, 'r');
        $is_flock = $block ? flock($fp, LOCK_EX) : flock($fp, LOCK_EX | LOCK_NB);
        $call_back = null;
        if ($is_flock)
        {
            $call_back = $func();
            flock($fp, LOCK_UN);
        }
        fclose($fp);
        return $call_back;
    }

    /**
     * 执行状态
     */
    public function executeStatus()
    {
        $file = $this->file;
        if (!file_exists($file))
        {
            return false;
        }
        $fp = fopen($file, "r");
        if (flock($fp, LOCK_EX | LOCK_NB))
        {
            return false;
        }
        return true;
    }
}