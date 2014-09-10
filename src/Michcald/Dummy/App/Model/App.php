<?php

namespace Michcald\Dummy\App\Model;

class App extends \Michcald\Dummy\Model
{
    private $name;

    private $title;

    private $description;

    private $isAdmin;

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

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle()
    {
        return $this->title;
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

    public function setIsAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;

        return $this;
    }

    public function getIsAdmin()
    {
        return $this->isAdmin;
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
        return $this->privateKey;
    }

    public function toArray()
    {
        return array(
            'id' => (int)$this->getId(),
            'name' => $this->name,
            'title' => $this->title,
            'description' => $this->description,
            'is_admin' => $this->isAdmin,
            'public_key' => $this->publicKey,
            'private_key' => $this->privateKey,
        );
    }

}