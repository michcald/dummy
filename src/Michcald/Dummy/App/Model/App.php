<?php

namespace Michcald\Dummy\App\Model;

class App extends \Michcald\Dummy\Model
{
    private $name;

    private $description;

    private $publicKey;
    
    private $privateKey;

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

    public function setPublicKey($publicKey)
    {
        $this->publicKey = $publicKey;

        return $this;
    }

    public function getPublicKey()
    {
        return $this->publicKey;
    }
    
    public function setPrivateKey($privateKey)
    {
        $this->privateKey = $privateKey;

        return $this;
    }

    public function getPrivateKey()
    {
        return $this->prviateKey;
    }

    public function toArray()
    {
        return array(
            'id' => (int)$this->getId(),
            'name' => $this->name,
            'description' => $this->description,
            'public_key' => $this->publicKey
        );
    }

}