<?php
namespace EasyTask\Socket;

class Server
{
    /**
     * 主机
     * @var string
     */
    private $host = '';

    /**
     * 端口
     * @var string
     */
    private $port = '';

    /**
     * 消息处理函数
     * @var null
     */
    public $onMessage = null;

    /**
     * 轮询处理函数
     * @var null
     */
    public $inTimeLoop = null;

    /**
     * 构造函数
     * @param string $host 主机
     * @param string $port 端口
     */
    public function __construct($host, $port)
    {
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * 监听服务
     * @throws \Exception
     */
    public function listen()
    {
        //服务地址
        $address = "tcp://{$this->host}}:{$this->host}";

        //创建连接
        $socket = stream_socket_server($address, $errno, $errstr);
        if (!$socket)
        {
            throw new \Exception("create server {$address} failure,errno:{$errno},errstr:{$errstr}");
        }

        //守护运行
        while (true)
        {
            //监听连接
            \set_error_handler(function () {
            });
            $client = stream_socket_accept($socket, 0, $peerName);
            \restore_error_handler();
            if ($client)
            {
                //读取client发送的信息
                $client_msg = '';
                while (!feof($client))
                {
                    $client_msg .= fgets($client, 128);
                }
                $client_msg = json_decode(base64_decode($client_msg), true);

                //消息处理函数
                call_user_func($this->onMessage, $client_msg);
            }

            //轮询处理函数
            call_user_func($this->inTimeLoop);
        }
    }
}