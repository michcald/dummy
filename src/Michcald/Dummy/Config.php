<?php

namespace Michcald\Dummy;

class Config
{
    private static $instance = null;
    
    private $data = array();
    
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
            throw new \Exception('Config file not found: ' . $filename);
        }
        
        $content = file_get_contents($filename);
        
        $data = \Symfony\Component\Yaml\Yaml::parse($content, true);
        
        $this->data = array_merge($this->data, $data);
        
        return $this->data;
    }

    public function __get($key)
    {
        return $this->data[$key];
    }
    
    public function getData()
    {
        return $this->data;
    }
}