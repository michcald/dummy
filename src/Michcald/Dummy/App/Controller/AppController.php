<?php

namespace Michcald\Dummy\App\Controller;

use Michcald\Dummy\Interfaces\Administrable;
use Michcald\Dummy\Controller\Crud;

use Michcald\Dummy\Response\Json as JsonResponse;

class AppController extends Crud implements Administrable
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

            $values = $form->getValues();
            
            $query = new \Michcald\Dummy\Dao\Query();
            $query->addWhere('name', $values['name']);
            $result = $this->dao->findOne($query);
            
            if ($result) {
                $response = new JsonResponse();
                $response->setStatusCode(409) // conflict
                    ->setContent(array(
                        'error' => array(
                            'status_code' => 409,
                            'message' => 'The app already exists'
                        )
                    ));
                return $response;
            }
            
            $values['public_key'] = hash('sha256', mt_rand());
            $values['private_key'] = hash('sha256', mt_rand() * rand(0, 10000));
            
            $app = $this->dao->create($values);

            $this->dao->persist($app);

            $response = new \Michcald\Dummy\Response\Json();
            $response->setStatusCode(201)
                ->setContent($app->getId());

            return $response;

        } else {

            $response = new \Michcald\Dummy\Response\Json();
            $response->setStatusCode(400)
                ->setContent(array(
                    'error' => array(
                        'status_code' => 400,
                        'message' => 'Data not valid',
                        'form' => $form->getErrorMessages()
                    )
                ));
            return $response;
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
