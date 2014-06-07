<?php

namespace Michcald\Dummy\Repository\Field;

class File extends \Michcald\Dummy\Repository\Field\String
{
    public function getDiscriminator()
    {
        return 'file';
    }
}