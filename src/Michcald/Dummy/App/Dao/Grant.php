<?php

namespace Michcald\Dummy\App\Dao;

class Grant extends \Michcald\Dummy\Dao
{
    public function create(array $row = null)
    {
        $grant = new \Michcald\Dummy\App\Model\Grant();

        if ($row) {
            $grant
                ->setAppId($row['app_id'])
                ->setRepositoryId($row['repository_id'])
                ->setCreate($row['create'])
                ->setRead($row['read'])
                ->setUpdate($row['update'])
                ->setDelete($row['delete']);

            if (isset($row['id'])) {
                $grant->setId($row['id']);
            }
        }

        return $grant;
    }

    public function getTable()
    {
        return 'meta_app_grant';
    }

}