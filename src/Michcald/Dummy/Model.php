<?php

namespace Michcald\Dummy;

abstract class Model
{
    private $id;
    
    final public function setId($id)
    {
        $this->id = $id;
        
        return $this;
    }
    
    final public function getId()
    {
        return $this->id;
    }
    
    abstract public function toArray();
}