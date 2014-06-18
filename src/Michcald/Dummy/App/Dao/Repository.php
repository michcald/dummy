<?php

namespace Michcald\Dummy\App\Dao;

use Michcald\Dummy\Model\Repository\Field;

class Repository extends \Michcald\Dummy\Dao
{
    public function getTable()
    {
        return 'meta_repository';
    }

    public function persist(\Michcald\Dummy\App\Model\Repository $repository)
    {
        parent::persist($repository);

        // TODO create/update fields

        // TODO create/alter table
    }

    public function delete($repository)
    {
        parent::delete($repository);

        // TODO delete the fields in meta_repo_fields

        // TODO drop table
    }

    public function create(array $row = null)
    {
        $repository = new \Michcald\Dummy\Model\Repository();

        if ($repository) {
            $repository
                ->setId($row['id'])
                ->setName($row['name'])
                ->setDescription($row['description'])
                ->setSingularLabel($row['label_singular'])
                ->setPluralLabel($row['label_plural']);

            // parents
            $parents = json_decode($row['parents'], true);
            foreach ($parents as $parent) {
                $repository->addParent($parent);
            }

            // children
            $children = json_decode($row['children'], true);
            foreach ($children as $child) {
                $repository->addChild($child);
            }

            // TODO load all the fields
        }

        return $repository;
    }
}
