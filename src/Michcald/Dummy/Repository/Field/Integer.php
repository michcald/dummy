<?php

namespace Michcald\Dummy\Repository\Field;

class Integer extends \Michcald\Dummy\Repository\Field
{
    public function getDiscriminator()
    {
        return 'integer';
    }
    
    public function toSQL()
    {
        $sql = $this->getName() . ' INT(11)';
        
        if ($this->isRequired()) {
            $sql .= ' NOT NULL';
        } else {
            $sql .= ' DEFAULT NULL';
        }
        
        return $sql;
    }
}