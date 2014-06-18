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
        $password = sha1($request->getHeader('PHP_AUTH_PW', false));

        $db = \Michcald\Mvc\Container::get('dummy.db');

        $apps = $db->fetchAll('SELECT name,password FROM meta_app');

        foreach ($apps as $app) {
            if ($user == $app['name'] && $password == $app['password']) {
                return;
            }
        }

        $response = new NotAuthorizedResponse();
        $response->send();
        die();
    }

}