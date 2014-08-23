<?php

namespace Michcald\Dummy\App\Controller\Administrable;

use Michcald\Dummy\Interfaces\Administrable;
use Michcald\Dummy\Controller\Crud;

use Michcald\Dummy\Response\Json as JsonResponse;
use Michcald\Dummy\Response\Error\NotFoundResponse;

class RepositoryController extends Crud implements Administrable
{
    private $dao;

    public function __construct()
    {
        $this->dao = new \Michcald\Dummy\App\Dao\Repository();
    }

    public function createAction()
    {
        // @TODO verify if the app is admin flagged

        $form = new \Michcald\Dummy\App\Form\Model\Repository();

        $form->setValues($this->getRequest()->getData());

        if ($form->isValid()) {

            $values = $form->getValues();

            // @TODO verify if already exists
            $query = new \Michcald\Dummy\Dao\Query();
            $query->addWhere('name', $values['name']);
            $result = $this->dao->findOne($query);

            if ($result) {
                $response = new JsonResponse();
                $response->setStatusCode(409) // conflict
                    ->setContent(array(
                        'error' => array(
                            'status_code' => 409,
                            'message' => 'The repository already exists'
                        )
                    ));
                return $response;
            }

            $repository = $this->dao->create($values);

            $this->dao->persist($repository);

            $response = new JsonResponse();
            $response->setStatusCode(201)
                ->setContent($repository->getId());

            return $response;

        } else {

            $values = $form->getValues();

            $formErrors = array();
            foreach ($form->getElements() as $element) {
                $formErrors[$element->getName()] = array(
                    'value' => $values[$element->getName()],
                    'errors' => $element->getErrorMessages()
                );
            }

            $response = new \Michcald\Dummy\Response\Json();
            $response->setStatusCode(400)
                ->setContent(array(
                    'error' => array(
                        'status_code' => 400,
                        'message' => 'Data not valid',
                        'form' => $formErrors
                    )
                ));
            return $response;
        }
    }

    public function updateAction($id)
    {
        // @TODO verify if the app is admin flagged
    }

    public function deleteAction($id)
    {
        // @TODO verify if the app is admin flagged
        $query = new \Michcald\Dummy\Dao\Query();
        $query->addWhere('id', $id);

        $repository = $this->dao->findOne($query);

        if (!$repository) {
            return new \Michcald\Dummy\Response\Json\NotFound('Repository not found: ' . $id);
        }

        $this->dao->delete($repository);

        $response = new \Michcald\Dummy\Response\Json();

        $response->setStatusCode(204);

        return $response;
    }

}