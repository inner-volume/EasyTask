<?php
namespace EasyTask\Queue\Driver;

/**
 * Class File
 * @package EasyTask
 */
class File
{
    public function lPush()
    {
        $a = new \Redis();
        $a->lPush();
    }
}
