<?php

namespace Michcald\Dummy\Repository\Field;

class Date extends \Michcald\Dummy\Repository\Field
{
    public function getDiscriminator()
    {
        return 'date';
    }
    
    public function toSQL()
    {
        $sql = $this->getName() . ' DATE';
        
        if ($this->isRequired()) {
            $sql .= ' NOT NULL';
        } else {
            $sql .= ' DEFAULT NULL';
        }
        
        return $sql;
    }
}