<?php

namespace Michcald\Dummy\Response\Error;

class InternalResponse extends AbstractResponse
{
    public function __construct($message = null)
    {
        parent::__construct();
        
        $this->setStatusCode(500);
        
        if (!$message) {
            $message = 'Internal error';
        }
        
        $this->setMessage($message);
    }
}