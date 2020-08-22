<?php
namespace EasyTask;

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
     */
    public function __construct($name = 'pipe')
    {
        //初始化文件
        $path = Helper::getLokPath();
        $this->file = $path . md5($name);
        if (!file_exists($this->file))
        {
            @file_put_contents($this->file, '');
        }
    }
}
