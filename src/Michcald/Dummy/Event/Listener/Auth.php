<?php

namespace Michcald\Dummy\Event\Listener;

use Michcald\Dummy\Response\Error\NotAuthorizedResponse;

class Auth implements \Michcald\Mvc\Event\SubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            'mvc.event.dispatch.pre' => 'auth'
        );
    }
    
    public function auth($event)
    {
        $request = $event->get('mvc.request');
        
        if ($request->isMethod('cli')) {
            return;
        }
        
        $user = $request->getHeader('PHP_AUTH_USER', false);
        $password = $request->getHeader('PHP_AUTH_PW', false);
        
        $config = \Michcald\Dummy\Config::getInstance();
        
        foreach ($config->auth as $auth) {
            if ($auth['user'] == $user && $auth['password'] == $password) {
                return;
            }
        }
        
        $response = new NotAuthorizedResponse();
        $response->send();
        die();
    }

}