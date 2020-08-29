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
    protected $protocol = 'tcp';

    /**
     * host
     * @var string
     */
    protected $host = '127.0.0.1';

    /**
     * port
     * @var string
     */
    protected $port = '8000';
}