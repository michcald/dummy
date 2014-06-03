<?php

namespace Michcald\Dummy\Response\Error;

class NotAuthorizedResponse extends AbstractResponse
{
    public function __construct($message = null)
    {
        parent::__construct();
        
        $this->setStatusCode(401);
        
        if (!$message) {
            $this->setMessage('Not authorized');
        }
    }
}