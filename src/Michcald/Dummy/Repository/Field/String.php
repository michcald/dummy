<?php

namespace Michcald\Dummy\Repository\Field;

class String extends \Michcald\Dummy\Repository\Field
{
    public function getDiscriminator()
    {
        return 'string';
    }
    
    public function toSQL()
    {
        $sql = $this->getName() . ' VARCHAR(255)';
        
        if ($this->isRequired()) {
            $sql .= ' NOT NULL';
        } else {
            $sql .= ' DEFAULT NULL';
        }
        
        return $sql;
    }
}