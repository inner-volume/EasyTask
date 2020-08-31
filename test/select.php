<?php

//创建Socket
$socket = stream_socket_server("tcp://127.0.0.1:80", $errno, $errStr);
if (!$socket)
{
    throw new Exception("创建Tcp服务失败,errno:{$errno},errStr:{$errStr}");
}

//设置非阻塞
stream_set_blocking($socket, false);
while (true)
{
    \set_error_handler(function () {
    });
    $conn = stream_socket_accept($socket, -1, $peerName);
    \restore_error_handler();
    if ($conn)
    {
        var_dump(111);
        //读取client发送的信息
        $client_msg = '';
        while (!feof($conn))
        {
            var_dump(!feof($conn));
            sleep(1);
            $client_msg .= fgets($conn, 1024);
        }
        echo 'client_msg:' . $client_msg . PHP_EOL;

        //发送消息给client
        $text = "hello world" . PHP_EOL;
        fwrite($conn, $text);
        fclose($conn);
    }
}