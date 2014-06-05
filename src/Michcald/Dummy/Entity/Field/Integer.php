<?php

namespace Michcald\Dummy\Entity\Field;

class Integer extends \Michcald\Dummy\Entity\Field
{
    public function getDiscriminator()
    {
        return 'integer';
    }
}