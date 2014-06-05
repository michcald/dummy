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

$db = new Michcald\Db\Adapter(
    $config->database['adapter'], 
    $config->database['host'],
    $config->database['user'], 
    $config->database['password'], 
    $config->database['dbname']
);

$registry = \Michcald\Dummy\RepositoryRegistry::getInstance();

foreach ($config->repositories as $r) {
    $repository = new \Michcald\Dummy\Repository($r['name']);
    
    $repository->setDescription($r['description'])
            ->setSingularLabel($r['label']['singular'])
            ->setPluralLabel($r['label']['plural'])
            ->setDb($db);
    
    foreach ($r['parents'] as $p) {
        $repository->addParent($p);
    }
    
    foreach ($r['children'] as $c) {
        $repository->addParent($c);
    }
    
    foreach ($r['fields'] as $f) {
        $field = null;
        if ($f['type'] == 'string') {
            $field = new Michcald\Dummy\Entity\Field\String($f['name']);
        } else if ($f['type'] == 'text') {
            $field = new \Michcald\Dummy\Entity\Field\Text($f['name']);
        } else if ($f['type'] == 'file') {
            $field = new \Michcald\Dummy\Entity\Field\File($f['name']);
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

$request = new \Michcald\Dummy\Request();

$mvc->run($request);






