<?php

namespace Michcald\Dummy\App\Controller\Repository;

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
                $response = new JsonResponse();
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

    public function deleteAction($id) {

    }

    public function listAction()
    {
        $page = $this->getRequest()->getQueryParam('page', '1');
        $limit = $this->getRequest()->getQueryParam('limit', '30');
        $filters = $this->getRequest()->getQueryParam('filters', array());
        $orders = $this->getRequest()->getQueryParam('orders', array());

        $form = new \Michcald\Dummy\App\Form\ListForm();
        $form->setFilters(array(
            'repository_id',
        ));
        $form->setOrders(array(
            'display_order'
        ));

        $form->setValues(array(
            'page' => $page,
            'limit' => $limit,
            'filters' => $filters,
            'orders' => $orders
        ));

        if ($form->isValid()) {

            $values = $form->getValues();

            $paginator = new \Michcald\Paginator();
            $paginator->setItemsPerPage($values['limit'])
                ->setCurrentPageNumber($values['page']);

            $entityQuery = new \Michcald\Dummy\Dao\Query();
            $entityQuery->setLimit($paginator->getLimit())
                ->setOffset($paginator->getOffset());

            foreach ($values['filters'] as $filter) {
                $entityQuery->addWhere($filter['field'], $filter['value']);
            }

            foreach ($values['orders'] as $order) {
                $entityQuery->addOrder($order['field'], $order['direction']);
            }

            $daoResult = $this->dao->findAll($entityQuery);

            $paginator->setTotalItems($daoResult->getTotalHits());

            $array = array(
                'paginator' => array(
                    'page' => array(
                        'current' => $paginator->getCurrentPageNumber(),
                        'total'   => $paginator->getNumberOfPages()
                    ),
                    'results' => $paginator->getTotalItems()
                ),
                'results' => array()
            );

            foreach ($daoResult->getResults() as $field) {
                $array['results'][] = $field->toArray();
            }

            $response = new \Michcald\Dummy\Response\Json();
            $response->setStatusCode(200)
                ->setContent($array);
            return $response;

        } else {
            return new \Michcald\Dummy\Response\Json\BadRequest(
                $form->getErrorMessages());
        }
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