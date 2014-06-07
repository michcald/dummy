<?php

include '../vendor/autoload.php';

$config = \Michcald\Dummy\Config::getInstance();
$config->loadDir('../app/config');

if ($config->env == 'dev') { // forse fare dev.php
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

$db = null;
if (isset($config->database)) {
    $db = new Michcald\Db\Adapter(
        $config->database['adapter'], 
        $config->database['host'],
        $config->database['user'], 
        $config->database['password'], 
        $config->database['dbname']
    );
    
    \Michcald\Mvc\Container::add('dummy.db', $db);
}

$registry = \Michcald\Dummy\RepositoryRegistry::getInstance();

if (isset($config->repositories)) {
    foreach ($config->repositories as $r) {
        $repository = new \Michcald\Dummy\Repository($r['name']);

        $repository->setDescription($r['description'])
                ->setSingularLabel($r['label']['singular'])
                ->setPluralLabel($r['label']['plural']);

        foreach ($r['parents'] as $p) {
            $repository->addParent($p);
        }

        foreach ($r['children'] as $c) {
            $repository->addChild($c);
        }

        foreach ($r['fields'] as $f) {
            $field = null;
            if ($f['type'] == 'string') {
                $field = new Michcald\Dummy\Repository\Field\String($f['name']);
            } else if ($f['type'] == 'text') {
                $field = new \Michcald\Dummy\Repository\Field\Text($f['name']);
            } else if ($f['type'] == 'file') {
                $field = new \Michcald\Dummy\Repository\Field\File($f['name']);
            } else if ($f['type'] == 'date') {
                $field = new \Michcald\Dummy\Repository\Field\Date($f['name']);
            } else if ($f['type'] == 'datetime') {
                $field = new \Michcald\Dummy\Repository\Field\Datetime($f['name']);
            }

            $field->setLabel($f['label'])
                ->setDescription($f['description'])
                ->setRequired($f['required'])
                ->setSearchable($f['searchable']);

            // add validators

            $repository->addField($field);
        }

        $registry->addRepository($repository);
    }
}

$mvc->addEventSubscriber(new \Michcald\Dummy\Event\Listener\Auth());

$request = new \Michcald\Dummy\Request();

$mvc->run($request);






