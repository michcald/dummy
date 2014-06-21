<?php

namespace Michcald\Dummy\App\Controller;

use Michcald\Dummy\Response\JsonResponse;
use Michcald\Dummy\Response\Error\NotFoundResponse;

class RepositoryController extends \Michcald\Dummy\Controller\Crud
{
    private $dao;

    public function __construct()
    {
        $this->dao = new \Michcald\Dummy\Dao\Repository();
    }

    public function createAction()
    {
        $form = new \Michcald\Dummy\Form\Repository();
        $form->setValues($this->getRequest()->getData());

        if ($form->isValid()) {
            $repository = $this->dao->create();

            $data = $form->getValues();

            $repository->setName($data['name'])
                ->setDescription($data['description'])
                ->setSingularLabel($data['label_singular'])
                ->setPluralLabel($data['label_plural']);

            $this->dao->persist($repository);

            $response = new JsonResponse();
            $response->setStatusCode(201)
                ->setContent(json_encode($repository->toArray()));

            return $response;

        } else {
            $array = array(
                'error' => array(
                    'status_code' => 400,
                    'message' => 'Wrong fields',
                    'errors' => $form->getErrorMessages()
                )
            );

            $response = new JsonResponse();
            $response->setStatusCode(400)
                ->setContent(json_encode($array));
            return $response;
        }
    }

    public function readAction($name)
    {
        $repository = $this->dao->find($name);

        if (!$repository) {
            return new NotFoundResponse('Repository not found: ' . $name);
        }

        $response = new JsonResponse();
        $response->setStatusCode(200)
            ->setContent(json_encode($repository->toArray()));

        return $response;
    }

    public function listAction() {

    }

    public function updateAction($name)
    {

    }

    public function deleteAction($name)
    {

    }

}