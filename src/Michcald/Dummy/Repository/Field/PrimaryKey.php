<?php

namespace Michcald\Dummy\Repository\Field;

class PrimaryKey extends \Michcald\Dummy\Repository\Field
{
    public function getDiscriminator()
    {
        return 'primary_key';
    }
    
    public function getLabel()
    {
        return 'ID';
    }
    
    public function toSQL()
    {
        return $this->getName() . ' INT(11) AUTO_INCREMENT, '
                . 'PRIMARY KEY (' . $this->getName() . ')';
    }
}