<?php

include '../vendor/autoload.php';

$config = \Michcald\Dummy\Config::getInstance();
try {
    $config->load('../app/config/parameters.yml');
    
    $config->load($config->config['routes']);
    $config->load($config->config['repositories']);

} catch (\Exception $e) {
    die ($e->getMessage());
}

if ($config->env == 'dev') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

$mvc = new \Michcald\Mvc\Mvc();

foreach ($config->routes as $routeConfig) {
    
    $uri = new \Michcald\Mvc\Router\Route\Uri();
    $uri->setPattern($routeConfig['uri']['pattern']);

    foreach ($routeConfig['uri']['requirements'] as $requirement) {
        $uri->setRequirement($requirement['param'], $requirement['value']);
    }
    
    $route = new \Michcald\Mvc\Router\Route();
    $route->setMethods($routeConfig['methods'])
        ->setUri($uri)
        ->setId($routeConfig['name'])
        ->setControllerClass($routeConfig['controller'])
        ->setActionName($routeConfig['action']);

    $mvc->addRoute($route);
}

// building the repos

// create repositoryProvider
// get() is lazy loading the config file, if the repo do not exist 500


$request = new \Michcald\Dummy\Request();

$mvc->run($request);






