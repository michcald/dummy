<?php

namespace Michcald\Dummy\App\Model\Repository\Field;

class ForeignKey extends \Michcald\Dummy\App\Model\Repository\Field
{
    public function getDiscriminator()
    {
        return 'foreign_key';
    }

    public function toSQL()
    {
        return $this->getName() . ' INT(11) NOT NULL';
    }
}