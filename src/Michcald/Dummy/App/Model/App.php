<?php

namespace Michcald\Dummy\App\Model;

class App extends \Michcald\Dummy\Model
{
    private $name;

    private $password;

    private $description;

    private $grants = array();

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
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

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function addGrant(App\Grant $grant)
    {
        $this->grants[] = $grant;

        return $this;
    }

    public function hasGrant(Repository $repository, $method)
    {
        foreach ($this->grants as $grant) {
            if ($grant->getRepository()->getName() == $repository->getName()) {
                if ($grant->getMethod($method)) {
                    return true;
                } else {
                    return false;
                }
            }
        }

        return false;
    }

    public function getGrants()
    {
        return $this->grants;
    }

    public function toArray()
    {
        return array(
            'name' => $this->name,
            'description' => $this->description,
            'password' => $this->password
        );
    }

}