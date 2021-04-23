<?php

/**
 * Think3.2.3 support
 */
class ThinkSupport
{
    /**
     * argv
     * @var mixed
     */
    private $argv;

    /**
     * argc
     * @var mixed
     */
    private $argc;

    /**
     * action
     * @var string
     */
    private $action;

    /**
     * force
     * @var string
     */
    private $force;

    /**
     * support constructor.
     */
    public function __construct()
    {
        //save cli Input
        $this->argv = $_SERVER['argv'];
        $this->argc = $_SERVER['argc'];

        //save the command and empty cli Input
        $this->action = isset($_SERVER['argv']['1']) ? $_SERVER['argv']['1'] : '';
        $this->force = isset($_SERVER['argv']['2']) ? $_SERVER['argv']['2'] : '';
        $_SERVER['argv'] = [] && $_SERVER['argc'] = 0;

        //suppress think's errors
        if (!isset($_SERVER['REMOTE_ADDR'])) $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        if (!isset($_SERVER['REQUEST_URI'])) $_SERVER['REQUEST_URI'] = 'localhost';
    }

    /**
     * load think's code
     * @param Closure $think
     * @return ThinkSupport
     */
    public function invokeThink($think)
    {
        ob_start();
        $think();
        ob_get_clean();
        return $this;
    }

    /**
     * include your code
     * @param Closure $code
     */
    public function invokeYourCode($code)
    {
        //recover cli Input.
        $_SERVER['argv'] = $this->argv;
        $_SERVER['argc'] = $this->argc;

        //invoke
        $code($this->action, $this->force);
    }
}

/**
 * Code start
 */
(new ThinkSupport())
    ->invokeThink(function () {
        //加载tp的代码
        require './index.php';
    })
    ->invokeYourCode(function ($action, $force) {
        // 加载Composer
        require './vendor/autoload.php';

        // $action值有start|status|stop

        // 编写你的代码
    });

/**
 * How to run ?
 * Use cmd or powerShell:
 * php ./index.php start|status|stop
 */