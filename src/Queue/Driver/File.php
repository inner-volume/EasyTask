<?php
namespace EasyTask\Queue\Driver;

/**
 * Class File
 * @package EasyTask
 */
class File
{


    public function lPusha()
    {
        $a = new \Redis();
        $a->lPush();
    }
}
