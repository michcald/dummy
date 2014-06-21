<?php

namespace Michcald\Dummy\App\Controller;

class AppController extends \Michcald\Dummy\Controller\Crud
{
    private $dao;

    public function __construct()
    {
        $this->dao = new \Michcald\Dummy\App\Dao\App();
    }

    public function createAction()
    {

    }

    public function deleteAction($id)
    {
        $query = new \Michcald\Dummy\Dao\Query();
        $query->addWhere('id', $id);

        $app = $this->dao->findOne($query);

        if (!$app) {
            return new \Michcald\Dummy\Response\Json\NotFound('App not found: ' . $id);
        }

        $this->dao->delete($app);

        $response = new \Michcald\Dummy\Response\Json();

        $response->setStatusCode(204);

        return $response;
    }

    public function listAction()
    {
        $query = new \Michcald\Dummy\Dao\Query();
        $query->addOrder('name', 'ASC');

        $apps = $this->dao->findAll($query);

        return $apps;
    }

    public function readAction($id)
    {
        $query = new \Michcald\Dummy\Dao\Query();
        $query->addWhere('id', $id);

        $app = $this->dao->findOne($query);

        if (!$app) {
            return new \Michcald\Dummy\Response\Json\NotFound('App not found: ' . $id);
        }

        $response = new \Michcald\Dummy\Response\Json();

        $response->setContent($app->toArray());

        return $response;
    }

    public function updateAction($id) {

    }

}
