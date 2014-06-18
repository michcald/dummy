<?php

namespace Michcald\Dummy\App\Model\Repository\Field;

class Text extends \Michcald\Dummy\App\Model\Repository\Field
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