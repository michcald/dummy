<?php

namespace Michcald\Dummy\App\Dao;

class Repository extends \Michcald\Dummy\Dao
{
    public function getTable()
    {
        return 'meta_repository';
    }

    public function persist($repository)
    {
        return parent::persist($repository);

        // TODO create/update fields

        // TODO create/alter table
    }

    public function delete($repository)
    {
        return parent::delete($repository);

        // TODO delete the fields in meta_repo_fields

        // TODO drop table
    }

    public function create(array $row = null)
    {
        $repository = new \Michcald\Dummy\App\Model\Repository();

        if ($row) {
            $repository->setName($row['name'])
                ->setDescription($row['description'])
                ->setSingularLabel($row['label_singular'])
                ->setPluralLabel($row['label_plural']);

            if (isset($row['id'])) {
                $repository->setId($row['id']);
            }
        }

        return $repository;
    }
}
