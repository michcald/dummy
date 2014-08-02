<?php

namespace Michcald\Dummy\App\Controller;

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

    public function readAction($id)
    {
        $query = new \Michcald\Dummy\Dao\Query();
        $query->addWhere('id', $id)
            ->setLimit(1);

        $repository = $this->dao->findOne($query);

        if (!$repository) {
            return new \Michcald\Dummy\Response\Json\NotFound('Repository not found: ' . $id);
        }

        $response = new \Michcald\Dummy\Response\Json();

        $response->setContent($repository->toArray());

        return $response;
    }

    public function listAction()
    {
        $page = $this->getRequest()->getQueryParam('page', '1');
        $limit = $this->getRequest()->getQueryParam('limit', '30');
        $query = $this->getRequest()->getQueryParam('query', '');
        $filters = $this->getRequest()->getQueryParam('filters', array());
        $orders = $this->getRequest()->getQueryParam('orders', array());

        $form = new \Michcald\Dummy\App\Form\ListForm();
        $form->setFilters(array(
            'name',
            'description',
        ));
        $form->setOrders(array(
            'name',
        ));

        $form->setValues(array(
            'page' => $page,
            'limit' => $limit,
            'query' => $query,
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

            foreach ($daoResult->getResults() as $app) {
                $array['results'][] = $app->toArray();
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

    public function updateAction($name)
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