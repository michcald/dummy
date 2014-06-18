<?php

namespace Michcald\Dummy\App\Model\Repository\Field;

class Boolean extends \Michcald\Dummy\App\Model\Repository\Field
{
    public function getDiscriminator()
    {
        return 'boolean';
    }

    public function toSQL()
    {
        $sql = $this->getName() . ' TINYINT(1)';

        if ($this->isRequired()) {
            $sql .= ' NOT NULL';
        } else {
            $sql .= ' DEFAULT NULL';
        }

        return $sql;
    }
}