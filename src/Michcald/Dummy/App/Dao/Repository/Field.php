<?php

namespace Michcald\Dummy\App\Dao\Repository;

abstract class Field extends \Michcald\Dummy\Dao
{
    public function create(Field $field, array $row)
    {
        $field->setId($row['id'])
            ->setName($row['name'])
            ->setLabel($row['label'])
            ->setDescription($row['description'])
            ->setDisplayOrder($row['display_order'])
            ->setRequired((bool) $row['required'])
            ->setMain((bool) $row['main'])
            ->setSearchable((bool) $row['searchable'])
            ->setList((bool) $row['list'])
            ->setSortable((bool) $row['sortable']);
    }

    public function getTable()
    {
        return 'meta_repository_field';
    }

}