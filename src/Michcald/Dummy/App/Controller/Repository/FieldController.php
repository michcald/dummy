<?php

namespace Michcald\Dummy\Controller\Repository;

use Michcald\Dummy\Interfaces\Administrable;
use Michcald\Dummy\Controller\Crud;

class FieldController extends Crud implements Administrable
{
    private $dao;

    public function __construct()
    {
        $this->dao = new \Michcald\Dummy\App\Dao\Repository\Field();
    }

    public function createAction() {

    }

    public function deleteAction($id) {

    }

    public function listAction($repositoryId)
    {
        $query = new \Michcald\Dummy\Dao\Query();
        $query->addOrder('display_order', 'ASC')
            ->setLimit(1000);

        $result = $this->dao->findAll($query);

        $array = array();
        foreach ($result->getResults() as $app) {
            $array[] = $app->toArray();
        }

        $response = new \Michcald\Dummy\Response\Json();
        $response->setContent($array);

        return $response;
    }

    public function readAction($id)
    {
        $query = new \Michcald\Dummy\Dao\Query();
        $query->addWhere('id', $id);

        $field = $this->dao->findOne($query);

        if (!$field) {
            return new \Michcald\Dummy\Response\Json\NotFound('Field not found: ' . $id);
        }

        $response = new \Michcald\Dummy\Response\Json();

        $response->setContent($field->toArray());

        return $response;
    }

    public function updateAction($id) {

    }

}