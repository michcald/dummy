<?php

namespace Michcald\Dummy\Event\Listener;

class Administrable implements \Michcald\Mvc\Event\SubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            'mvc.event.dispatch.pre' => 'grantAccess'
        );
    }

    public function grantAccess(\Michcald\Mvc\Event\Event $event)
    {
        $request = $event->get('mvc.request');

        /* @var $router \Michcald\Mvc\Router */
        $router = \Michcald\Mvc\Container::get('mvc.router');
        
        /* @var $route \Michcald\Mvc\Router\Route */
        $route = $router->route($request);
        
        $interfaces = class_implements($route->getControllerClass());
        
        if (in_array('Michcald\Dummy\Interfaces\Administrable', $interfaces)) {
            
        }
    }
}