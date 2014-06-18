<?php

namespace Michcald\Dummy\App\Model\App;

class Grant extends \Michcald\Dummy\Model
{
    private $repository;

    private $methods = array(
        'get'    => false,
        'post'   => false,
        'put'    => false,
        'delete' => false
    );

    public function setRepository(\Michcald\Dummy\Model\Repository $repository)
    {
        $this->repository = $repository;

        return $this;
    }

    public function setMethod($method, $value)
    {
        $this->methods[$method] = (bool)$value;

        return $this;
    }

    public function getMethod($method)
    {
        return $this->methods[$method];
    }

    public function toArray()
    {
        $array = array(
            'id'         => $this->getId(),
            'repository' => $this->repository
        );

        foreach ($this->methods as $method => $value) {
            $array[$method] = $value;
        }

        return $array;
    }

}