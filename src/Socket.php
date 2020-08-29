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
     * protocol
     * @var string
     */
    private $protocol;

    /**
     * host
     * @var string
     */
    public $host;

    /**
     * port
     * @var int
     */
    public $port;

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

    }
}