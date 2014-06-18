<?php

namespace Michcald\Dummy\App\Model\Repository\Field;

class File extends \Michcald\Dummy\App\Model\Repository\Field\String
{
    public function getDiscriminator()
    {
        return 'file';
    }
}