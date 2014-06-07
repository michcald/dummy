<?php

namespace Michcald\Dummy\Repository\Field;

class Text extends \Michcald\Dummy\Repository\Field
{
    public function getDiscriminator()
    {
        return 'text';
    }
    
    public function toSQL()
    {
        $sql = $this->getName() . ' TEXT';
        
        if ($this->isRequired()) {
            $sql .= ' NOT NULL';
        } else {
            $sql .= ' DEFAULT NULL';
        }
        
        return $sql;
    }
}