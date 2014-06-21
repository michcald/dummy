<?php

namespace Michcald\Dummy\App\Model;

class App extends \Michcald\Dummy\Model
{
    private $name;

    private $description;

    private $password;

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function toArray()
    {
        return array(
            'name' => $this->name,
            'description' => $this->description
        );
    }

}