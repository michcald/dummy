<?php

namespace Michcald\Dummy\Response\Json;

class NotAuthorized extends \Michcald\Dummy\Response\Json
{
    public function __construct($message = null)
    {
        parent::__construct();

        $this->setStatusCode(401);

        if (!$message) {
            $message = 'Not authorized';
        }

        parent::setContent(array(
            'status_code' => $this->getStatusCode(),
            'message' => $message
        ));
    }
}