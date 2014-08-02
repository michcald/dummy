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
            $updateSql = sprintf('UPDATE %s SET ', $this->getTable());

            $values = array();
            $chunks = array();
            foreach ($model->toArray() as $key => $value) {
                if ($key == 'id') {
                    continue;
                }
                $chunks[] = sprintf('`%s`=?', $key);
                $values[] = $value;
            }

            $updateSql .= implode(',', $chunks) . ' WHERE id=' . $model->getId();

            $s = $this->getDb()->prepare($updateSql);
            $s->execute($values);

        } else {

            $updateSql = sprintf('INSERT INTO %s ', $this->getTable());

            $chunks = array();
            foreach ($model->toArray() as $key => $value) {
                $chunks[] = sprintf('`%s`', $key);
            }

            $updateSql .= '(' . implode(',', $chunks) . ') VALUES (';

            $chunks = array();
            foreach ($model->toArray() as $key => $value) {
                $chunks[] = '?';
            }

            $updateSql .= implode(',', $chunks) . ');';

            $s = $this->getDb()->prepare($updateSql);
            $s->execute(array_values($model->toArray()));

            // last insert id
            /*$stm = $this->getDb()->prepare(
                sprintf('SELECT MAX(id) FROM %s', $this->getTable())
            );
            $stm->execute();
            $row = $stm->fetch();*/

            if (!$s) {
                throw new \Exception($this->getDb()->errorInfo());
            }

            $model->setId($this->getDb()->lastInsertId());
        }
    }

    public function delete($model)
    {
        $this->getDb()->query(
            sprintf(
                'DELETE FROM %s WHERE id=%d',
                $this->getTable(),
                $model->getId()
            )
        );
    }
}
