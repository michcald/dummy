<?php

namespace Michcald\Dummy\App\Model\Repository\Field;

class PrimaryKey extends \Michcald\Dummy\App\Model\Repository\Field
{
    public function getDiscriminator()
    {
        return 'primary_key';
    }

    public function toSQL()
    {
        return $this->getName() . ' INT(11) AUTO_INCREMENT, '
            . 'PRIMARY KEY (' . $this->getName() . ')';
    }
}