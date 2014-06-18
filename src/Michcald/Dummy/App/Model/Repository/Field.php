<?php

namespace Michcald\Dummy\App\Model\Repository;

abstract class Field extends \Michcald\Dummy\Model
{
    private $name;

    private $label;

    private $description;

    private $required = false;

    private $searchable = false;

    private $sortable = false;

    private $main = false;

    private $list = false;

    private $displayOrder;

    abstract public function getDiscriminator();

    abstract public function toSQL();

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
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

    public function setSortable($sortable)
    {
        $this->sortable = (bool)$sortable;

        return $this;
    }

    public function isSortable()
    {
        return $this->sortable;
    }

    public function setMain($main)
    {
        $this->main = (bool)$main;

        return $this;
    }

    public function isMain()
    {
        return $this->main;
    }

    public function setList($list)
    {
        $this->list = (bool)$list;

        return $this;
    }

    public function isList()
    {
        return $this->list;
    }

    public function setDisplayOrder($displayOrder)
    {
        $this->displayOrder = (int)$displayOrder;

        return $this;
    }

    public function getDisplayOrder()
    {
        return $this->displayOrder;
    }

    public function getValidationErrors()
    {
        return $this->validationErrors;
    }

    public function addValidator(\Michcald\Validator $validator)
    {
        $this->validators[] = $validator;

        return $this;
    }

    public function validate($value)
    {
        $this->validationErrors = array();

        if ($this->isRequired() && !$value) {
            if ($this->getDiscriminator() == 'boolean') {
                $value = 0;
            } else {
                $this->validationErrors[] = 'Required field';
                return false;
            }
        }

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
            'searchable' => $this->isSearchable(),
            'sortable' => $this->isSortable(),
            'list'        => $this->isList(),
            'display_order' => $this->getDisplayOrder()
        );

        return $array;
    }
}
