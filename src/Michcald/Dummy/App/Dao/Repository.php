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
        $db = $this->getDb();

        $stm = $db->prepare(
            'SELECT id app_id '
            . 'FROM meta_app '
            . 'WHERE id NOT IN ('
                . 'SELECT app_id FROM meta_app_grant WHERE repository_id=:repositoryId'
            . ')');
        $stm->execute(array(
            'repositoryId' => $repository->getId()
        ));

        $db->beginTransaction();

        parent::persist($repository);

        // create the related table
        $sql = sprintf(
            'CREATE TABLE IF NOT EXISTS %s (`id` INTEGER NOT NULL AUTO_INCREMENT,PRIMARY KEY (`id`));',
            $repository->getName()
        );
        $db->query($sql);

        // for every application create a record for the grants
        foreach ($stm->fetchAll(\PDO::FETCH_ASSOC) as $app) {
            $stm2 = $db->prepare(
                'INSERT INTO meta_app_grant (`app_id`,`repository_id`,`create`,`read`,`update`,`delete`) '
                . 'VALUES (?,?,?,?,?,?)');
            $stm2->execute(array(
                $app['app_id'],
                $repository->getId(),
                $app['app_id'] == 1 ? 1 : 0,
                $app['app_id'] == 1 ? 1 : 0,
                $app['app_id'] == 1 ? 1 : 0,
                $app['app_id'] == 1 ? 1 : 0
            ));
        }

        $db->commit();
    }

    public function delete($repository)
    {
        $db = $this->getDb();

        $db->beginTransaction();

        $sql = sprintf(
            'DROP TABLE IF EXISTS %s;',
            $repository->getName()
        );

        $db->query($sql);

        // remove all the grants on cascade

        parent::delete($repository);

        $db->commit();
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

    public function findAllGranted(\Michcald\Dummy\App\Model\App $app, array $filters, array $orders, $limit = 30, $offset = 0)
    {
        $orderBy = array();
        foreach ($orders as $field => $direction) {
            $orderBy[] = sprintf('%s %s', $field, $direction);
        }
        if (count($orderBy) > 0) {
            $orderBy = ' AND ' . implode(',', $orderBy);
        } else {
            $orderBy = '';
        }

        $where = array();
        foreach ($filters as $field => $value) {
            $where[] = sprintf('%s=%s', $field, $value);
        }
        if (count($where) > 0) {
            $where = ' AND ' . implode(' AND ', $where);
        } else {
            $where = '';
        }

        $sql = sprintf('SELECT t2.* FROM meta_app_grant t1,meta_repository t2 '
            . 'WHERE t1.repository_id=t2.id AND t1.read=1 AND t1.app_id=? %s %s',
            $orderBy,
            $where
        );

        $stm = $this->getDb()->prepare($sql);
        $stm->execute(array(
            $app->getId()
        ));

        $daoResult = new \Michcald\Dummy\Dao\Result();
        $daoResult->setTotalHits($stm->rowCount());

        $results = $stm->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($results as $result) {
            $model = $this->create($result);
            $daoResult->addResult($model);
        }

        return $daoResult;
    }
}
