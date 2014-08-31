<?php

namespace Michcald\Dummy\App\Model;

class Entity extends \Michcald\Dummy\Model
{
    private $repository;

    private $vars = array();

    public function setRepository(Repository $repository)
    {
        $this->repository = $repository;

        return $this;
    }

    public function getRepository()
    {
        return $this->repository;
    }

    public function setValues(array $values)
    {
        $this->vars = $values;

        return $this;
    }

    public function getValues()
    {
        return $this->vars;
    }

    public function __set($key, $value)
    {
        $this->vars[$key] = $value;
    }

    public function __get($key)
    {
        if (array_key_exists($key, $this->vars)) {
            return $this->vars[$key];
        }

        return null;
    }


    public function toArray2($includeId = true)
    {
        $array = array();

        foreach ($this->repository->getFields() as $field) {

            $fieldName = $field->getName();

            if ($fieldName == 'id' && !$includeId) {
                continue;
            }

            if (array_key_exists($fieldName, $this->vars)) {
                $array[$fieldName] = $this->vars[$fieldName];
            } else {
                $array[$fieldName] = null;
            }
        }

        return $array;
    }

    public function toArray()
    {
        $array = array();
        $array['id'] = (int) $this->getId();

        return array_merge($array, $this->vars);
    }

}
