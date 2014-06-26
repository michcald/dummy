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
        $form = new \Michcald\Dummy\App\Form\Model\App();

        $form->setValues($this->getRequest()->getData());

        if ($form->isValid()) {

            $app = $this->dao->create($form->getValues());

            $this->dao->persist($app);

            $response = new \Michcald\Dummy\Response\Json();
            $response->setStatusCode(201)
                ->setContent($app->getId());

            return $response;

        } else {

            return new \Michcald\Dummy\Response\Json\BadRequest(
                $form->getErrorMessages()
            );
        }
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
        $query->addOrder('name', 'ASC')
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
