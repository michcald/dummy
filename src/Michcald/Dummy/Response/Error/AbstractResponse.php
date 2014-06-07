<?php

namespace Michcald\Dummy\Response\Error;

use Michcald\Dummy\Response\JsonResponse;

abstract class AbstractResponse extends JsonResponse
{
    private $message;
    
    public function __construct($message = null)
    {
        $this->addHeader('Content-Type: application/json');
        
        if ($this->message) {
            $this->message = $message;
        }
    }
    
    public function setMessage($message)
    {
        $this->message = $message;
        
        return $this;
    }
    
    public function getMessage()
    {
        return $this->message;
    }
    
    public function getContent()
    {
        $content = array(
            'error' => array(
                'status_code' => $this->getStatusCode(),
                'message'     => $this->getMessage()
            )
        );
        
        $json = json_encode($content);
        
        return $json;
    }
}