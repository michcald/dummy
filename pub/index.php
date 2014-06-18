<?php

include '../vendor/autoload.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

$config = \Michcald\Dummy\Config::getInstance();
$config->loadDir('../app/config');

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

$db = new Michcald\Db\Adapter(
    $config->database['adapter'],
    $config->database['host'],
    $config->database['user'],
    $config->database['password'],
    $config->database['dbname']
);

\Michcald\Mvc\Container::add('dummy.db', $db);

$mvc->addEventSubscriber(new \Michcald\Dummy\App\Event\Listener\Auth());

$request = new \Michcald\Dummy\Request();

$mvc->run($request);






