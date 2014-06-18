<?php

namespace Michcald\Dummy\Controller;

use Michcald\Dummy\Response\JsonResponse;
use Michcald\Dummy\Response\Error\NotFoundResponse;

use Michcald\Dummy\RepositoryRegistry;

class EntityController extends \Michcald\Mvc\Controller\HttpController
{
    private $repositoryDao;
    private $dao;

    public function __construct()
    {
        $this->dao = new \Michcald\Dummy\Dao\Entity();
        $this->repositoryDao = new \Michcald\Dummy\Dao\Repository();
    }

    public function listAction($repository)
    {
        $repo = $this->repositoryDao->find($repository);

        if (!$repo) {
            return new NotFoundResponse('Repository not found: ' . $repository);
        }

        $page = (int)$this->getRequest()->getQueryParam('page', 1);
        $limit = (int)$this->getRequest()->getQueryParam('limit', 30);
        $query = $this->getRequest()->getQueryParam('query', '');
        $filters = $this->getRequest()->getQueryParam('filters', array());
        $orders = $this->getRequest()->getQueryParam('orders', array());

        $form = new \Michcald\Dummy\Form\Entity\ListForm();
        $form->setRepository($repo);

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

            $daoQuery = new \Michcald\Dummy\DaoQuery\ListQuery();

            $daoQuery->setRepository($repo)
                ->setLimit($paginator->getLimit())
                ->setOffset($paginator->getOffset());

            foreach ($values['filters'] as $filter) {
                $daoQuery->addWhere($filter['field'], $filter['value']);
            }

            $daoQuery->setQuery($values['query']);

            foreach ($values['orders'] as $order) {
                $daoQuery->addOrder($order['field'], $order['direction']);
            }

            $daoResult = $this->dao->findBy($daoQuery);

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

            foreach ($daoResult->getResults() as $entity) {
                $array['results'][] = $entity->toArray();
            }

            $response = new JsonResponse();
            $response->setStatusCode(200)
                ->setContent(json_encode($array));
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

    public function readAction($repository, $id)
    {
        $repo = $this->repositoryDao->find($repository);

        if (!$repo) {
            return new NotFoundResponse('Repository not found: ' . $repository);
        }

        $daoQuery = new \Michcald\Dummy\DaoQuery\FindQuery();
        $daoQuery->setRepository($repo)
            ->setId($id);

        $entity = $this->dao->find($daoQuery);

        if (!$entity) {
            return new NotFoundResponse('Entity not found: ' . $id);
        }

        $response = new JsonResponse();
        $response->setStatusCode(200)
            ->setContent(json_encode($entity->toArray()));

        return $response;
    }

    public function createAction($repository)
    {

    }

    public function updateAction($repository, $id)
    {

    }

    public function deleteAction($repository, $id)
    {

    }
}
