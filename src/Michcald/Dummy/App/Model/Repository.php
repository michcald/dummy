<?php

namespace Michcald\Dummy\App\Model;

class Repository extends \Michcald\Dummy\Model
{
    private $name;

    private $description;

    private $singularLabel;

    private $pluralLabel;

    private $fields = array();

    public function addField(Repository\Field $field)
    {
        $this->fields[$field->getName()] = $field;

        return $this;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function hasField($name)
    {
        return array_key_exists($name, $this->fields);
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

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setSingularLabel($singularLabel)
    {
        $this->singularLabel = $singularLabel;

        return $this;
    }

    public function getSingularLabel()
    {
        return $this->singularLabel;
    }

    public function setPluralLabel($pluralLabel)
    {
        $this->pluralLabel = $pluralLabel;

        return $this;
    }

    public function getPluralLabel()
    {
        return $this->pluralLabel;
    }

    public function toArray()
    {
        $array = array(
            'id' => $this->getId(),
            'name' => $this->getName(),
            'label_singular' => $this->getSingularLabel(),
            'label_plural' => $this->getPluralLabel(true),
            'description' => $this->getDescription(),
            //'parents' => $this->getParents(),
            //get'children' => $this->getChildren(),
            //'fields' => array()
        );

        foreach ($this->fields as $field) {
            //$array['fields'][] = $field->toArray();
        }

        return $array;
    }
}
