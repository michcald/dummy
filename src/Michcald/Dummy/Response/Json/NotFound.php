<?php

namespace Michcald\Dummy\Response\Json;

class NotFound extends \Michcald\Dummy\Response\Json
{
    public function __construct($message = null)
    {
        parent::__construct();

        $this->setStatusCode(404);

        if (!$message) {
            $message = 'Not found';
        }

        parent::setContent(array(
            'status_code' => $this->getStatusCode(),
            'message' => $message
        ));
    }
}