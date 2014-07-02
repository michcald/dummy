<?php

namespace Michcald\Dummy;

abstract class Dao
{
    /**
     * @return \PDO
     */
    final public function getDb()
    {
        return \Michcald\Mvc\Container::get('dummy.db');
    }

    public function findOne(\Michcald\Dummy\Dao\Query $query)
    {
        $query->setTable($this->getTable());

        $selectQuery = $query->getSelectQuery();

        try {
            $statement = $this->getDb()->prepare($selectQuery);
            $statement->execute();
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return null;
        }

        if (!$result) {
            return null;
        }

        $model = $this->create($result);

        $model->setId($result['id']);

        return $model;
    }

    public function findAll(\Michcald\Dummy\Dao\Query $query)
    {
        $query->setTable($this->getTable());

        $countQuery = $query->getCountQuery();
        $statement = $this->getDb()->prepare($countQuery);
        $statement->execute();
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
        $totalHits = $result['count'];

        $selectQuery = $query->getSelectQuery();

        $statement = $this->getDb()->prepare($selectQuery);
        $statement->execute();
        $results = $statement->fetchAll(\PDO::FETCH_ASSOC);

        $daoResult = new \Michcald\Dummy\Dao\Result();
        $daoResult->setTotalHits($totalHits);

        foreach ($results as $result) {
            $model = $this->create($result);
            $daoResult->addResult($model);
        }

        return $daoResult;
    }

    abstract public function create(array $row = null);

    abstract public function getTable();

    public function persist($model)
    {
        if ($model->getId()) {
            $this->getDb()->update(
                $this->getTable(),
                $model->toArray(),
                'id=' . (int)$model->getId()
            );
        } else {
            $id = $this->getDb()->insert(
                $this->getTable(),
                $model->toArray()
            );

            $model->setId($id);
        }
    }

    public function delete($model)
    {
        $this->getDb()->delete(
            $this->getTable(),
            'id=' . (int) $model->getId()
        );
    }
}
