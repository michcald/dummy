<?php

namespace Michcald\Dummy\Dao;

class App extends \Michcald\Dummy\Dao
{
    public function getTable()
    {
        return 'meta_app';
    }

    public function create(array $row = null)
    {
        $app = new \Michcald\Dummy\App\Model\App();

        if ($row) {
            $app->setId($row['id'])
                ->setName($row['name'])
                ->setDescription($row['description'])
                ->setPassword($row['password']);
        }

        return $app;
    }

    public function delete(\Michcald\Dummy\App\Model\App $app)
    {
        // TODO delete all the grants
    }

    public function persist(\Michcald\Dummy\App\Model\App $app)
    {
        parent::persist($app);

        // TODO update/create also the grants
    }

    public function validate(\Michcald\Dummy\App\Model\App $app)
    {
        if (!$app->getName() || !$app->getPassword()) {
            return false;
        }

        return true;
    }

}