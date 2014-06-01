<?php

namespace Michcald\Dummy;

abstract class Repository
{
    private $fields = array();

    public function __construct()
    {
        $id = new Entity\Field\Integer('id');
        // add validation
        $this->addField($id);

        foreach ($this->getParentEntities() as $entity) {
            $foreignKey = new Entity\Field\Integer($entity . '_id');
            $foreignKey
                ->setLabel('')
                ->setDescription('')
                ->setExpose(true)
                ->setSearchable(false);
            $this->addField($foreignKey);
        }
    }

    protected function addField(Entity\Field $field)
    {
        $this->fields[] = $field;

        return $this;
    }

    public function getFields()
    {
        return $this->fields;
    }

    abstract public function getName();

    public function getDescription()
    {
        return '';
    }

    abstract public function getLabel($plural = false);

    abstract public function getMaxRecords();

    public function getParentEntities()
    {
        return array();
    }

    public function getChildEntitites()
    {
        return array();
    }

    public function count()
    {
        return $this->db->fetchOne(
                'SELECT COUNT(id) FROM ' . $this->getName());
    }

    private function validate(Entity $entity)
    {
        foreach ($this->fields as $field) {

            $fieldName = $field->getName();

            if ($fieldName == 'id') {
                continue;
            }

            if ($field->validate($entity->$fieldName)) {
                return false;
            }
        }

        return true;
    }

    public function create()
    {
        $entity = new Entity($this);

        return $entity;
    }

    public function persist(Entity $entity)
    {
        if (!$this->validate($entity)) {
            return false;
        }

        if (!$entity->id) {
            if ($this->getMaxRecords() && $this->count() < $this->getMaxRecords()) {
                $id = $this->db->insert(
                    $this->getName(),
                    $entity->toArray(false)
                );
                $entity->id = $id;
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->update(
                $this->getName(),
                $entity->toArray(false),
                'id=' . $entity->id
            );
            return true;
        }
    }

    public function toArray()
    {
        $array = array(
            'name' => $this->getName(),
            'label' => array(
                'singular' => $this->getLabel(),
                'plural' => $this->getLabel(true)
            ),
            'description' => $this->getDescription(),
            'max_records' => $this->getMaxRecords(),
            'parents' => $this->getParentEntities(),
            'children' => $this->getChildEntitites(),
            'fields' => array()
        );

        foreach ($this->fields as $field) {
            $array['fields'][] = $field->toArray();
        }

        return $array;
    }
}