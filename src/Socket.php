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
     * @var null
     */
    private $socket = null;

    /**
     * onMessageHand
     * @var Closure
     */
    public $onMessage = null;

    /**
     * @param string $protocol
     * @param string $host
     * @param int $port
     */
    public function start($protocol = 'tcp', $host = '127.0.0.1', $port = 8000)
    {
        $this->socket = stream_socket_server("{$protocol}://{$host}:{$port}", $errno, $errStr, STREAM_SERVER_BIND);
        if (!$this->socket)
        {
            throw new Exception("failed to create socket,errNo:{$errno},errStr:{$errStr}");
        }
    }
}