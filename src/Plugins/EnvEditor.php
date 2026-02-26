<?php

namespace MaterialDesignForum\Plugins;

class EnvEditor
{
    protected $path;
    protected $content = [];
    
    public function __construct($path = '.env')
    {
        $this->path = $path;
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
    }
    
    public function set($key, $value)
    {
        $this->content[$key] = $value;
        return $this;
    }
    
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