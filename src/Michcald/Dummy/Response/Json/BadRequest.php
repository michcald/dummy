<?php

namespace Michcald\Dummy\Response\Json;

class BadRequest extends \Michcald\Dummy\Response\Json
{
    public function __construct($message = null)
    {
        parent::__construct();

        $this->setStatusCode(400);

        if (!$message) {
            $message = 'Bad request';
        }

        parent::setContent(array(
            'error' => array(
                'status_code' => $this->getStatusCode(),
                'message' => $message
            )
        ));
    }
}