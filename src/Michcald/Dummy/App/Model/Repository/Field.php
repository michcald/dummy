<?php

namespace Michcald\Dummy\App\Model\Repository;

class Field extends \Michcald\Dummy\Model
{
    private $repositoryId;

    private $type;

    private $name;

    private $label;

    private $description;

    private $required = false;

    private $searchable = false;

    private $sortable = false;

    private $main = false;

    private $list = false;

    private $displayOrder;

    private $options;

    public function setRepositoryId($repositoryId)
    {
        $this->repositoryId = $repositoryId;

        return $this;
    }

    public function getRepositoryId()
    {
        return $this->repositoryId;
    }

    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

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
        $this->required = (int)$required;

        return $this;
    }

    public function isRequired()
    {
        return $this->required;
    }

    public function setSearchable($searchable)
    {
        $this->searchable = (int)$searchable;

        return $this;
    }

    public function isSearchable()
    {
        return $this->searchable;
    }

    public function setSortable($sortable)
    {
        $this->sortable = (int)$sortable;

        return $this;
    }

    public function isSortable()
    {
        return $this->sortable;
    }

    public function setMain($main)
    {
        $this->main = (int)$main;

        return $this;
    }

    public function isMain()
    {
        return $this->main;
    }

    public function setList($list)
    {
        $this->list = (int)$list;

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

    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function validate($value)
    {
        $this->validationErrors = array();

        if ($this->isRequired() && !$value) {
            if ($this->getType() == 'boolean') {
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
            'id' => $this->getId(),
            'repository_id' => $this->repositoryId,
            'name' => $this->name,
            'type' => $this->type,
            'options' => $this->options,
            'label' => $this->label,
            'description' => $this->description,
            'required' => $this->required,
            'searchable' => $this->searchable,
            'sortable' => $this->sortable,
            'list' => $this->list,
            'main' => $this->main,
            'display_order' => $this->displayOrder
        );

        return $array;
    }
}
