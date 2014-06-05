<?php

namespace Michcald\Dummy\Entity\Field;

class File extends \Michcald\Dummy\Entity\Field
{
    public function getDiscriminator()
    {
        return 'file';
    }
}