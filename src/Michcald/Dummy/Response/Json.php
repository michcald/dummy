<?php

namespace Michcald\Dummy\Response;

class Json extends \Michcald\Mvc\Response
{
    public function __construct()
    {
        $this->addHeader('Content-Type: application/json');
    }

    public function setContent($content)
    {
        if (is_array($content)) {
            $content = json_encode($content);
        }

        return parent::setContent($content);
    }
}