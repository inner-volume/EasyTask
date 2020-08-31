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
     * socket
     * @var null
     */
    protected $socket = null;

    /**
     * onMessageHand
     * @var Closure
     */
    public $onMessage = null;

    /**
     * @param string $protocol
     * @param string $host
     * @param int $port
     * @throws Exception
     */
    public function start($protocol = 'tcp', $host = '0.0.0.0', $port = 8000)
    {
        $this->socket = stream_socket_server("{$protocol}://{$host}:{$port}", $errno, $errStr);
        if (!$this->socket)
        {
            throw new Exception("failed to create socket,errNo:{$errno},errStr:{$errStr}");
        }

        stream_set_blocking($this->socket, false);

        while ($conn = stream_socket_accept($this->socket))
        {
            fwrite($conn, 'The local time is ' . date('n/j/Y g:i a') . "\n");
            fclose($conn);
        }
        fclose($this->socket);
    }
}