<?php

namespace Michcald\Dummy\App\Model;

class Param extends \Michcald\Dummy\Model
{
    private $name;

    private $value;

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function toArray()
    {
        return array(
            'name' => $this->name,
            'value' => $this->value
        );
    }

}