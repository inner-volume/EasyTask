<?php
namespace EasyTask;

use Exception;

/**
 * Class Command
 * @package EasyTask
 */
class Command
{
    /**
     * 通讯管道
     */
    private $pipe;

    /**
     * 构造函数
     * @param string $name ('manage同步|manage+scheduler异步')
     * @throws
     */
    public function __construct($name = 'pipe')
    {
        $this->pipe = new Pipe($name);
    }

    /**
     * 发送命令
     * @param array $command
     * @return int|false
     * @throws
     */
    public function send($command)
    {
        $command['time'] = time();
        $command = base64_encode(json_encode($command));
        return $this->pipe->write($command);
    }

    /**
     * 接收命令
     * @return array
     * @throws Exception
     */
    public function receive()
    {
        $command = $this->pipe->write();
        $command = json_decode(base64_decode($command));
        if (!$command)
        {
            return [];
        }

        return $command;
    }
}