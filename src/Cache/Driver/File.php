<?php
namespace EasyTask\Cache\Driver;

use EasyTask\Env;
use EasyTask\Helper\FileHelper;
use EasyTask\Lock;
use EasyTask\Cache\Driver;
use Exception;

/**
 * Class File
 * @package EasyTask
 */
class File extends Driver
{
    /**
     * 存储文件
     * @var string
     */
    private $file;

    /**
     * 默认配置
     * @var array
     */
    protected $options = [
        'prefix' => '',
    ];

    /**
     * 构造函数
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->options = array_merge($this->options, $options);
    }

    /**
     * 初始化
     * @param $name
     * @throws Exception
     */
    private function init($name)
    {
        $path = FileHelper::getQuePath();
        $file = $path . '%s.dat';
        $this->file = sprintf($file, md5($this->options['prefix'] . $name));
        if (!file_exists($this->file))
        {
            if (!file_put_contents($this->file, '[]', LOCK_EX | LOCK_NB))
            {
                throw new Exception("create file queue driver file:{$this->file} failed");
            }
        }
    }

    /**
     * rPop
     * @param string $key
     * @return bool|string|void
     * @throws Exception
     */
    public function rPop($key)
    {
        $this->init($key);
        $lock = new Lock($key);
        return $lock->execute(function () {
            //read
            $content = file_get_contents($this->file);
            $queue_data = $content ? json_decode($content, true) : [];
            $queue_data = is_array($queue_data) ? $queue_data : [];

            //shift+write
            $value = array_shift($queue_data);
            if (!file_put_contents($this->file, json_encode($queue_data)))
            {
                throw new Exception("rPop from file queue driver file:{$this->file} failed");
            }
            return $value;
        }, false);
    }

    /**
     * lPush
     * @param string $key
     * @param string $value
     * @return bool|void
     * @throws Exception
     */
    public function lPush($key, $value)
    {
        $this->init($key);
        $lock = new Lock($key);
        return $lock->execute(function () use ($value) {
            //read
            $content = file_get_contents($this->file);
            $queue_data = $content ? json_decode($content, true) : [];
            $queue_data = is_array($queue_data) ? $queue_data : [];

            //write
            array_push($queue_data, $value);
            if (!file_put_contents($this->file, json_encode($queue_data)))
            {
                throw new Exception("lPush to file queue driver file:{$this->file} failed");
            }
            return true;
        });
    }
}
