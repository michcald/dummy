<?php

namespace Michcald\Dummy;

class Config
{
    private static $instance = null;
    
    private $data;
    
    private function __construct() {}
    
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Config();
        }
        
        return self::$instance;
    }
    
    public function load($filename)
    {
        if (!file_exists($filename)) {
            throw new \Exception('Config file not found');
        }
        
        $content = file_get_contents($filename);
        
        $this->data = \Symfony\Component\Yaml\Yaml::parse($content, true);
        
        return $this->data;
    }

    public function __get($key)
    {
        return $this->data[$key];
    }
}