<?php

require 'D:\wwwroot\EasyTask/vendor/autoload.php';
//require '../vendor/autoload.php';


//实例化Task
$task = new \EasyTask\Task();

//提取命令行传参的命令
$argv = $_SERVER['argv'];
if (!empty($argv['1']))
{
    if ($argv['1'] == 'start')
    {
        //启动命令
        $task->setDaemon(true)->addFunc(function () {

            echo date('Y-m-d H:i:s', time()) . PHP_EOL;

        }, 'request', 10, 1)->start();

    }
    if ($argv['1'] == 'status')
    {
        //状态命令
        $task->status();
    }
    if ($argv['1'] == 'stop')
    {
        //停止命令
        $force = false;
        if (!empty($argv['2']) && $argv['2'] == '-f')
        {
            $force = true;
        }
        $task->stop($force);
    }
}