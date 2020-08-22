<?php
namespace EasyTask\Queue;

/**
 * Class Queue
 * @package EasyTask
 */
class Queue
{
    public function lPush()
    {
        $a= new \Redis();
        $a->lPush();
    }
}
