<?php

namespace Michcald\Dummy\Event\Listener;

class Auth implements \Michcald\Mvc\Event\SubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            'mvc.event.dispatch.pre' => 'grantAccess'
        );
    }

    public function grantAccess(\Michcald\Mvc\Event\Event $event)
    {
        /* @var $request \Michcald\Dummy\Request */
        $request = $event->get('mvc.request');

        $publicKey = $request->getHeader('HTTP_X_DUMMY_PUBLIC', false);
        $privateKey = $request->getHeader('HTTP_X_DUMMY_PRIVATE', false);

        if (!$publicKey) {
            $response = new \Michcald\Dummy\Response\Json();
            $response->setStatusCode(403)
                ->setContent('Not Authorized')
                ->send();
            die;
        }

        /* @var $db \PDO */
        $db = \Michcald\Mvc\Container::get('dummy.db');

        $stm = $db->prepare('SELECT * FROM meta_app WHERE public_key=?');
        $stm->execute(array(
            $publicKey
        ));

        $app = $stm->fetch(\PDO::FETCH_ASSOC);

        if (!$app) {
            $response = new \Michcald\Dummy\Response\Json();
            $response->setStatusCode(403)
                ->setContent('Not Authorized')
                ->send();
            die;
        }

        $dao = new \Michcald\Dummy\App\Dao\App();
        $appModel = $dao->create($app);

        if ($appModel->getPrivateKey() != $privateKey) {
            $response = new \Michcald\Dummy\Response\Json();
            $response->setStatusCode(403)
                ->setContent('Not Authorized')
                ->send();
            die;
        }

        \Michcald\Mvc\Container::add('dummy.app', $appModel);
    }
}