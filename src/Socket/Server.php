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
    public $onMessge = null;

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
     * 监听信息
     * @param array $data
     * @param int $timeOut
     * @return array
     * @throws \Exception
     */
    public function listen($data = [], $timeOut = 30)
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

                //传递给处理函数
                call_user_func($this->onMessge, $client_msg);
            }
        }
    }
}