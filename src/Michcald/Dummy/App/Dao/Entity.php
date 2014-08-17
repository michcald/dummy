<?php

namespace Michcald\Dummy\App\Dao;

class Entity extends \Michcald\Dummy\Dao
{
    private $repository;

    public function setRepository(\Michcald\Dummy\App\Model\Repository $repository)
    {
        $this->repository = $repository;

        return $this;
    }

    public function getTable()
    {
        return $this->repository->getName();
    }

    public function create(array $row = null)
    {
        $entity = new \Michcald\Dummy\App\Model\Entity();
        $entity->setRepository($this->repository);

        if ($row) {
            if (isset($row['id'])) {
                $entity->setId($row['id']);
                unset($row['id']);
            }
            $entity->setValues($row);
        }

        return $entity;
    }

    public function persist($entity)
    {
        // TODO save and substitute files with string

        return parent::persist($entity);
    }

    public function delete($entity)
    {
        // TODO remove children and files

        parent::delete($entity);
    }
}
