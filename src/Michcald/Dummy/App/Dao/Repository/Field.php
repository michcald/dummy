<?php

namespace Michcald\Dummy\App\Dao\Repository;

class Field extends \Michcald\Dummy\Dao
{
    public function create(array $row = null)
    {
        $field = new \Michcald\Dummy\App\Model\Repository\Field();

        if ($row) {
            $field->setId((int) $row['id'])
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

        return $field;
    }

    public function getTable()
    {
        return 'meta_repository_field';
    }

}