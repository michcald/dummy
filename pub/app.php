<?php

define('ENV', 'prod');

include '../vendor/autoload.php';

ini_set('display_errors', 0);

Michcald\Dummy\Bootstrap::init();

$mvc = \Michcald\Mvc\Container::get('dummy.mvc');

$request = \Michcald\Mvc\Container::get('dummy.mvc.request');

$mvc->run($request);





