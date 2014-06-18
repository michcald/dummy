<?php

namespace Michcald\Dummy\App\Model;

class Repository extends \Michcald\Dummy\Model
{
    private $name;

    private $description;

    private $singularLabel;

    private $pluralLabel;

    private $fields = array();

    private $parents = array();

    private $children = array();

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

    public function addParent($parent)
    {
        $this->parents[] = $parent;

        return $this;
    }

    public function getParents()
    {
        return $this->parents;
    }

    public function addChild($child)
    {
        $this->children[] = $child;

        return $this;
    }

    public function getChildren()
    {
        /*
        $registry = RepositoryRegistry::getInstance();

        $children = array();

        foreach ($this->children as $child) {

            $repo = $registry->getRepository($child);

            $children[] = array(
                'repository' => $child,
                'label' => array(
                    'singular' => $repo->getSingularLabel(),
                    'plural'   => $repo->getPLuralLabel()
                ),
            );
        }

        return $children;*/
    }

    public function toArray()
    {
        $array = array(
            'name' => $this->getName(),
            'label' => array(
                'singular' => $this->getSingularLabel(),
                'plural' => $this->getPluralLabel(true)
            ),
            'description' => $this->getDescription(),
            'parents' => $this->getParents(),
            'children' => $this->getChildren(),
            'fields' => array()
        );

        foreach ($this->fields as $field) {
            $array['fields'][] = $field->toArray();
        }

        return $array;
    }
}
