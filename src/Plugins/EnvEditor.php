<?php

/**
 * Author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster
 * @copyright Copyright (c) 2023 HaiGeMaster
 * @start-date 2023/05/20-15:53:29
 */

namespace MaterialDesignForum\Plugins;

class EnvEditor
{
    protected string $path;
    protected string $exampleEnvPath;
    protected array $content = [];
    /**
     * 构造函数
     * @param string $path .env文件路径
     */
    public function __construct(string $path = '.env')
    {
        $this->path = $path;
        $this->exampleEnvPath = $path . '.example';
        $this->load();
    }
    
    protected function load()
    {
        if (!file_exists($this->path)) {
            return;
        }
        
        $lines = file($this->path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue; // 跳过注释
            }
            
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $this->content[trim($key)] = trim($value);
            }
        }

        //如果exampleEnvPath存在，则读取一遍里面的键值，如果不在.env中，则写入更新进去
        // if (file_exists($this->exampleEnvPath)) {
        //     $exampleLines = file($this->exampleEnvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        //     foreach ($exampleLines as $line) {
        //         if (strpos(trim($line), '#') === 0) {
        //             continue; // 跳过注释
        //         }
        //         if (strpos($line, '=') !== false) {
        //             list($key, $value) = explode('=', $line, 2);
        //             if (!isset($this->content[trim($key)])) {
        //                 $this->content[trim($key)] = trim($value);
        //             }
        //         }
        //     }
        // }
    }
    /**
     * 设置环境变量
     * @param string $key 环境变量键
     * @param string $value 环境变量值
     * @return EnvEditor
     */
    public function set(string $key, string $value)
    {
        $this->content[$key] = $value;
        return $this;
    }
    /**
     * 保存环境变量
     * @return bool 是否成功
     */
    public function save(): bool
    {
        $lines = [];
        foreach ($this->content as $key => $value) {
            $lines[] = "{$key}={$value}";
        }
        
        return file_put_contents($this->path, implode("\n", $lines)) !== false;
    }
}

// 使用示例
// $env = new EnvEditor('.env');
// $env->set('APP_NAME', 'My Application')
//     ->set('APP_ENV', 'production')
//     ->save();