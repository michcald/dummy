<?php

namespace Michcald\Dummy\App\Event\Listener;

use Michcald\Dummy\Response\Json\NotAuthorized;

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

        // validate input
        $form = new \Michcald\Dummy\App\Form\Auth();
        $form->setValues(array(
            'user' => $user,
            'password' => $password
        ));

        if (!$form->isValid()) {
            $response = new \Michcald\Dummy\Response\Json\BadRequest($form->getErrorMessages());
            $response->send();
            die();
        }

        $values = $form->getValues();

        $appDao = new \Michcald\Dummy\App\Dao\App();

        $query = new \Michcald\Dummy\Dao\Query();
        $query->addWhere('name', $values['user'])
            ->addWhere('password', sha1($values['password']));

        $app = $appDao->findOne($query);

        if (!$app) {
            $response = new NotAuthorized();
            $response->send();
            die();
        }
    }

}