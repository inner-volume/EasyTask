<?php
namespace EasyTask\Helper;

use EasyTask\Env;
use Exception;

/**
 * Class TimerHelper
 * @package EasyTask
 */
class FileHelper
{
    /**
     * 获取运行时目录
     * @return  string
     * @throws Exception
     */
    public static function getRunTimePath()
    {
        $path = Env::get('runTimePath') ? Env::get('runTimePath') : sys_get_temp_dir();
        if (!is_dir($path))
        {
            throw new Exception('please set runTimePath');
        }
        $path = $path . DIRECTORY_SEPARATOR . Env::get('prefix') . DIRECTORY_SEPARATOR;
        $path = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $path);
        return $path;
    }

    /**
     * 获取Win进程目录
     * @return  string
     * @throws Exception
     */
    public static function getWinPath()
    {
        return static::getRunTimePath() . 'Win' . DIRECTORY_SEPARATOR;
    }

    /**
     * 获取日志目录
     * @return  string
     * @throws Exception
     */
    public static function getLogPath()
    {
        return static::getRunTimePath() . 'Log' . DIRECTORY_SEPARATOR;
    }

    /**
     * 获取进程命令通信目录
     * @return  string
     * @throws Exception
     */
    public static function getCsgPath()
    {
        return static::getRunTimePath() . 'Csg' . DIRECTORY_SEPARATOR;
    }

    /**
     * 获取进程管道目录
     * @return  string
     * @throws Exception
     */
    public static function getPiePath()
    {
        return static::getRunTimePath() . 'Pie' . DIRECTORY_SEPARATOR;
    }

    /**
     * 获取进程锁目录
     * @return  string
     * @throws Exception
     */
    public static function getLokPath()
    {
        return static::getRunTimePath() . 'Lok' . DIRECTORY_SEPARATOR;
    }

    /**
     * 获取标准输入输出目录
     * @return  string
     * @throws Exception
     */
    public static function getStdPath()
    {
        return static::getRunTimePath() . 'Std' . DIRECTORY_SEPARATOR;
    }

    /**
     * 初始化所有目录
     * @throws Exception
     */
    public static function initAllPath()
    {
        $paths = [
            static::getRunTimePath(),
            static::getWinPath(),
            static::getLogPath(),
            static::getLokPath(),
            static::getPiePath(),
            static::getCsgPath(),
            static::getStdPath(),
        ];
        foreach ($paths as $path)
        {
            if (!is_dir($path))
            {
                if (!mkdir($path, 0777, true))
                {
                    throw new Exception("Failed to create $path directory, please check permissions");
                }
            }
        }
    }
}