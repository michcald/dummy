<?php

namespace Michcald\Dummy\Entity\Field;

class Text extends \Michcald\Dummy\Entity\Field
{
    public function getDiscriminator()
    {
        return 'text';
    }
}