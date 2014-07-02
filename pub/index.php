<?php

include '../vendor/autoload.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

\Michcald\Dummy\Bootstrap::init();

$mvc = \Michcald\Mvc\Container::get('dummy.mvc');

$request = \Michcald\Mvc\Container::get('dummy.mvc.request');

$mvc->run($request);






