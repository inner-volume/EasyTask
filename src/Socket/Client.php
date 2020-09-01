<?php
namespace EasyTask\Socket;

use Exception;

class Client
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
     * 发送信息
     * @param array $data
     * @param int $timeOut
     * @return array
     * @throws Exception
     */
    public function send($data = [], $timeOut = 30)
    {
        //请求地址
        $address = "tcp://{$this->host}}:{$this->host}";

        //创建连接
        $socket = stream_socket_client($address, $errno, $errStr, $timeOut);
        if (!$socket)
        {
            throw new Exception("connection {$address} failure,errno:{$errno},errStr:{$errStr}");
        }

        //发送数据(换行符WIN|LINUX待验证)
        $data = json_encode($data);
        if (!$data)
        {
            throw new Exception('message json encode failed');
        }
        $data = base64_encode($data);
        if (!$data)
        {
            throw new Exception('message base64 encode failed');
        }
        if (!fwrite($socket, $data . PHP_EOL))
        {
            throw new Exception("write to {$address} failure");
        }

        //获取响应数据
        $response = '';
        while (!feof($socket))
        {
            $response .= fgets($socket, 128);
        }
        if ($response)
        {
            return json_decode(base64_decode($response), true);
        }
        return [];
    }
}