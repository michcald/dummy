<?php

namespace Michcald\Dummy;

class RepositoryRegistry
{
    private static $instance = null;
    
    private $repositories = array();
    
    private function __construct() {}
    
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new RepositoryRegistry();
        }
        
        return self::$instance;
    }
    
    public function addRepository(Repository $repository)
    {
        $this->repositories[$repository->getName()] = $repository;
        
        return $this;
    }
    
    public function getRepository($name)
    {
        if (array_key_exists($name, $this->repositories)) {
            return $this->repositories[$name];
        }
        
        throw new \Exception('Invalid repository: ' . $name);
    }
    
    public function getRepositories()
    {
        return $this->repositories;
    }
}