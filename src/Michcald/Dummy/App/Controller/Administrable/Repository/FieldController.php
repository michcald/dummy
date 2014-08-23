<?php

namespace Michcald\Dummy\App\Controller\Administrable\Repository;

use Michcald\Dummy\Interfaces\Administrable;
use Michcald\Dummy\Controller\Crud;

class FieldController extends Crud implements Administrable
{
    private $dao;

    public function __construct()
    {
        $this->dao = new \Michcald\Dummy\App\Dao\Repository\Field();
    }

    public function createAction()
    {
        $form = new \Michcald\Dummy\App\Form\Model\Repository\Field();

        $form->setValues($this->getRequest()->getData());

        if ($form->isValid()) {

            $values = $form->getValues();

            $query = new \Michcald\Dummy\Dao\Query();
            $query->addWhere('name', $values['name'])
                ->addWhere('repository_id', $values['repository_id']);
            $result = $this->dao->findOne($query);

            if ($result) {
                $response = new \Michcald\Dummy\Response\Json();
                $response->setStatusCode(409) // conflict
                    ->setContent(array(
                        'error' => array(
                            'status_code' => 409,
                            'message' => 'The field already exists'
                        )
                    ));
                return $response;
            }

            $field = $this->dao->create($values);

            $this->dao->persist($field);

            $response = new \Michcald\Dummy\Response\Json();
            $response->setStatusCode(201)
                ->setContent($field->getId());

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

    public function deleteAction($id)
    {
        // @TODO verify if the app is admin flagged
        $query = new \Michcald\Dummy\Dao\Query();
        $query->addWhere('id', $id);

        $field = $this->dao->findOne($query);

        if (!$field) {
            return new \Michcald\Dummy\Response\Json\NotFound('Field not found: ' . $id);
        }

        $this->dao->delete($field);

        $response = new \Michcald\Dummy\Response\Json();

        $response->setStatusCode(204);

        return $response;
    }

    public function updateAction($id) {

    }

}