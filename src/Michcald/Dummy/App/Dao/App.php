<?php

namespace Michcald\Dummy\App\Dao;

class App extends \Michcald\Dummy\Dao
{
    public function create(array $row = null)
    {
        $app = new \Michcald\Dummy\App\Model\App();

        if ($row) {
            $app->setName($row['name'])
                ->setDescription($row['description'])
                ->setPublicKey($row['public_key'])
                ->setPrivateKey($row['private_key'])
                ->setIsAdmin($row['is_admin']);

            if (isset($row['id'])) {
                $app->setId($row['id']);
            }
        }

        return $app;
    }

    public function persist($app)
    {
        $db = $this->getDb();

        $stm = $db->prepare(
            'SELECT id repository_id '
            . 'FROM meta_repository '
            . 'WHERE id NOT IN ('
                . 'SELECT repository_id FROM meta_app_grant WHERE app_id=:appId'
            . ')');
        $stm->execute(array(
            'appId' => $app->getId()
        ));

        $db->beginTransaction();

        parent::persist($app);

        // for every application create a record for the grants
        foreach ($stm->fetchAll(\PDO::FETCH_ASSOC) as $r) {
            $stm2 = $db->prepare('INSERT INTO meta_app_grant (app_id,repository_id) VALUES (?,?)');
            $stm2->execute(array(
                $app->getId(),
                $r['repository_id'],
            ));
        }

        $db->commit();
    }

    public function getTable()
    {
        return 'meta_app';
    }

}