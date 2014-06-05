<?php

namespace Michcald\Dummy\Entity;

abstract class Field
{
    private $name;

    private $value;

    private $label;

    private $description;

    private $required = false;

    private $searchable = false;

    //private $exposable = false;

    //private $list = false;

    //private $main = false;

    private $validators = array();

    public function __construct($name)
    {
        $this->name = $name;
    }
    
    abstract public function getDiscriminator();

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

    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel()
    {
        return $this->label;
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

    public function setRequired($required)
    {
        $this->required = (bool)$required;

        return $this;
    }

    public function isRequired()
    {
        return $this->required;
    }

    public function setSearchable($searchable)
    {
        $this->searchable = (bool)$searchable;

        return $this;
    }

    public function isSearchable()
    {
        return $this->searchable;
    }

    public function addValidator(\Michcald\Validator $validator)
    {
        $this->validators[] = $validator;

        return $this;
    }

    public function validate($value)
    {
        foreach ($this->validators as $validator) {
            if (!$validator->validate($value)) {
                // define error messages
                return false;
            }
        }

        return true;
    }

    public function toArray()
    {
        $array = array(
            'name' => $this->getName(),
            'type' => $this->getDiscriminator(),
            'label' => $this->getLabel(),
            'description' => $this->getDescription(),
            'required' => $this->isRequired(),
            'searchable' => $this->isSearchable()
        );

        return $array;
    }
}