<?php

namespace Michcald\Dummy\Event\Listener;

class Monolog implements \Michcald\Mvc\Event\SubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            'mvc.event.dispatch.pre' => 'accessLog'
        );
    }

    public function accessLog(\Michcald\Mvc\Event\Event $event)
    {
        $request = $event->get('mvc.request');

        /* @var \Monolog\Logger $logger */
        /*$logger = \Michcald\Mvc\Container::get('dummy.monolog');

        $log = sprintf('%s %s', $request->getMethod(), $request->getUri());

        $logger->addInfo(
            $log,
            $request->getHeaders()
        );*/
    }
}