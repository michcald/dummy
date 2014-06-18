<?php

namespace Michcald\Dummy\Response\Error;

class NotFoundResponse extends AbstractResponse
{
    public function __construct($message = null)
    {
        parent::__construct();

        $this->setStatusCode(404);

        if (!$message) {
            $message = 'Not found';
        }

        $this->setMessage($message);
    }
}