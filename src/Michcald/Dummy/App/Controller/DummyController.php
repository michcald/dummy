<?php

namespace Michcald\Dummy\App\Controller;

class DummyController extends \Michcald\Mvc\Controller\HttpController
{
    public function notFoundAction($any)
    {
        $message = 'Route not found for URI: ' . $any;

        return new \Michcald\Dummy\Response\Json\NotFound($message);
    }
}