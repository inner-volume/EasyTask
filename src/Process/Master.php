<?php
namespace EasyTask\Process;

use EasyTask\Env;
use EasyTask\Helper;
use Exception;

/**
 * Class Process
 * @package EasyTask\Process
 */
class Master extends Process
{
    /**
     * @throws Exception
     */
    public function start()
    {
        if (!Helper::isWin()){
            //异步处理
            if (Env::get('daemon')){
                Helper::setMask();
                $this->unix_fork(function () {
                    $this->unix_setLeader();
                    (new Manage())->execute();
                }, function () {
                    $this->unix_wait();
                    $this->status();
                });
            }

            //同步处理
            (new Manage())->execute();
            exit();
        }

    }

    public function status()
    {

    }

    public function stop($force)
    {

    }
}