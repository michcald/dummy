<?php

namespace Michcald\Dummy\App\Dao;

class Param extends \Michcald\Dummy\Dao
{
    public function getTable()
    {
        return 'meta_param';
    }

    public function create(array $row = null)
    {
        $param = new \Michcald\Dummy\Model\Param();

        if ($row) {
            $param->setId($row['id'])
                ->setName($row['name'])
                ->setValue($row['value']);
        }

        return $param;
    }

}