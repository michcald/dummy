<?php

namespace Michcald\Dummy\App\Controller;

class EntityController extends \Michcald\Mvc\Controller\HttpController
{
    private $repositoryDao;
    private $dao;

    public function __construct()
    {
        $this->dao = new \Michcald\Dummy\App\Dao\Entity();
        $this->repositoryDao = new \Michcald\Dummy\App\Dao\Repository();
    }

    private function findRepository($repositoryName)
    {
        $repoQuery = new \Michcald\Dummy\Dao\Query();
        $repoQuery->addWhere('name', $repositoryName);

        return $this->repositoryDao->findOne($repoQuery);
    }

    public function listAction($repositoryName)
    {
        $repository = $this->findRepository($repositoryName);

        if (!$repository) {
            return new \Michcald\Dummy\Response\Json\NotFound('Repository not found: ' . $repositoryName);
        }

        $this->dao->setRepository($repository);

        $page = $this->getRequest()->getQueryParam('page', '1');
        $limit = $this->getRequest()->getQueryParam('limit', '30');
        $query = $this->getRequest()->getQueryParam('query', '');
        $filters = $this->getRequest()->getQueryParam('filters', array());
        $orders = $this->getRequest()->getQueryParam('orders', array());

        $form = new \Michcald\Dummy\App\Form\ListForm();
        $form->setRepository($repository);

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

            foreach ($repository->getFields() as $field) {
                if ($field->isSearchable()) {
                    $entityQuery->addLike($field->getName(), $values['query']);
                }
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

            foreach ($daoResult->getResults() as $entity) {
                $array['results'][] = $entity->toArray();
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

    public function readAction($repositoryName, $id)
    {
        $repository = $this->findRepository($repositoryName);

        if (!$repository) {
            return new \Michcald\Dummy\Response\Json\NotFound('Repository not found: ' . $repositoryName);
        }

        $this->dao->setRepository($repository);

        $entityQuery = new \Michcald\Dummy\Dao\Query();
        $entityQuery->addWhere('id', $id);

        $entity = $this->dao->findOne($entityQuery);

        if (!$entity) {
            return new \Michcald\Dummy\Response\Json\NotFound('Entity not found: ' . $id);
        }

        $response = new \Michcald\Dummy\Response\Json();
        $response->setStatusCode(200)
            ->setContent($entity->toArray());

        return $response;
    }

    public function createAction($repositoryName)
    {
        $repository = $this->findRepository($repositoryName);

        if (!$repository) {
            return new \Michcald\Dummy\Response\Json\NotFound('Repository not found: ' . $repositoryName);
        }

        $this->dao->setRepository($repository);

        $form = new \Michcald\Dummy\App\Form\Model\Entity(
            $repository
        );

        $form->setValues($this->getRequest()->getData());

        if ($form->isValid()) {

            $entity = $this->dao->create($form->getValues());

            $this->dao->persist($entity);

            $response = new \Michcald\Dummy\Response\Json();
            $response->setStatusCode(201)
                ->setContent($entity->getId());

            return $response;

        } else {

            return new \Michcald\Dummy\Response\Json\BadRequest(
                $form->getErrorMessages()
            );

        }
    }

    public function updateAction($repositoryName, $id)
    {
        $repository = $this->findRepository($repositoryName);

        if (!$repository) {
            return new \Michcald\Dummy\Response\Json\NotFound('Repository not found: ' . $repositoryName);
        }

        $form = new \Michcald\Dummy\App\Form\Model\Entity(
            $repository
        );

        $form->setValues($entity->toArray());

        // use the form
    }

    public function deleteAction($repositoryName, $id)
    {
        $repository = $this->findRepository($repositoryName);

        if (!$repository) {
            return new \Michcald\Dummy\Response\Json\NotFound('Repository not found: ' . $repositoryName);
        }

        $this->dao->setRepository($repository);

        $query = new \Michcald\Dummy\Dao\Query();
        $query->addWhere('id', $id);

        $entity = $this->dao->findOne($query);

        if (!$entity) {
            return new \Michcald\Dummy\Response\Json\NotFound('Entity not found: ' . $id);
        }

        $this->dao->delete($entity);

        $response = new \Michcald\Dummy\Response\Json();

        $response->setStatusCode(204);

        return $response;
    }
}
