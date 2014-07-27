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
        
        $stm = $db->prepare('SELECT app_id FROM meta_app_grant WHERE repository_id=:repoId');
        $stm->execute(array(
            'repoId' => $repository->getId()
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
            $db->prepare('INSERT INTO meta_app_grant (app_id,repository_id) VALUES (?,?)');
            $db->exec(array(
                $app['app_id'],
                $repository->getId()
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
}
