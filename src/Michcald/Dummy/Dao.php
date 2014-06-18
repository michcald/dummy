<?php

namespace Michcald\Dummy;

abstract class Dao
{
    /**
     * @return \Michcald\Db\Adapter
     */
    final public function getDb()
    {
        return \Michcald\Mvc\Container::get('dummy.db');
    }

    final public function findOne(\Michcald\Dummy\Dao\Query $query)
    {
        $result = $this->getDb()->fetchRow($query->getSql());

        if (!$result) {
            return null;
        }

        return $this->create($result);
    }

    final public function findAll(\Michcald\Dummy\Dao\Query $query)
    {
        $countQuery = $query->getCountQuery();
        $totalHits = $this->getDb()->fetchOne($countQuery);

        $selectQuery = $query->getSelectQuery();
        $results = $this->getDb()->fetchAll($selectQuery);

        $daoResult = new \Michcald\Dummy\DaoResult();
        $daoResult->setTotalHits($totalHits);

        foreach ($results as $result) {
            $model = $this->create($result);
            $daoResult->addResult($model);
        }

        return $daoResult;
    }

    abstract public function create(array $row = null);

    abstract public function getTable();

    public function persist(Model $model)
    {
        if (!$this->validate($model)) {
            throw new \Exception('Not valid model');
        }

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

    public function delete(Model $model)
    {
        if (!$this->validate($model)) {
            throw new \Exception('Invalid model');
        }

        $this->getDb()->delete(
            $this->getTable(),
            'id=' . (int) $model->getId()
        );
    }

    public function validate(Model $model = null)
    {
        if ($model) {
            return true;
        }

        return false;
    }

}
