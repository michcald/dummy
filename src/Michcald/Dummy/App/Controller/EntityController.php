<?php

namespace Michcald\Dummy\App\Controller;

class EntityController extends \Michcald\Mvc\Controller\HttpController
{
    private $repositoryDao;
    private $repositoryFieldDao;
    private $dao;

    public function __construct()
    {
        $this->dao = new \Michcald\Dummy\App\Dao\Entity();
        $this->repositoryDao = new \Michcald\Dummy\App\Dao\Repository();
        $this->repositoryFieldDao = new \Michcald\Dummy\App\Dao\Repository\Field();
    }

    private function findRepository($repositoryId)
    {
        $repoQuery = new \Michcald\Dummy\Dao\Query();
        $repoQuery->addWhere('id', $repositoryId);

        return $this->repositoryDao->findOne($repoQuery);
    }

    public function listAction($repositoryId)
    {
        $repository = $this->findRepository($repositoryId);

        if (!$repository) {
            return new \Michcald\Dummy\Response\Json\NotFound('Repository not found: ' . $repositoryId);
        }

        $this->dao->setRepository($repository);

        $page = $this->getRequest()->getQueryParam('page', '1');
        $limit = $this->getRequest()->getQueryParam('limit', '30');
        $searchQuery = $this->getRequest()->getQueryParam('query', '');
        $filters = $this->getRequest()->getQueryParam('filters', array());
        $orders = $this->getRequest()->getQueryParam('orders', array());

        $form = new \Michcald\Dummy\App\Form\ListForm();

        $form->setValues(array(
            'page' => $page,
            'limit' => $limit,
            'query' => $searchQuery,
            'filters' => $filters,
            'orders' => $orders
        ));

        $query = new \Michcald\Dummy\Dao\Query();
        $query->addWhere('repository_id', $repositoryId)
            ->setLimit(10000);

        $availableFilters = array();
        $availableSorters = array();

        $fields = $this->repositoryFieldDao->findAll($query);
        foreach ($fields->getResults() as $f) {
            /* @var $f \Michcald\Dummy\App\Model\Repository\Field */
            if (!$f->getType() == 'foreign') {
                continue;
            }
            $availableFilters[] = $f->getName();

            if ($f->isSortable()) {
                $availableSorters[] = $f->getName();
            }
        }

        $form->setFilters($availableFilters);
        $form->setOrders($availableSorters);

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

            if ($searchQuery) {
                $searchableCount = 0;
                foreach ($fields->getResults() as $field) {
                    if ($field->isSearchable()) {
                        $entityQuery->addLike($field->getName(), $values['query']);
                        $searchableCount++;
                    }
                }

                if ($searchableCount == 0) {
                    $entityQuery->addWhere('0', 1);
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

    public function readAction($repositoryId, $id)
    {
        $repository = $this->findRepository($repositoryId);

        if (!$repository) {
            return new \Michcald\Dummy\Response\Json\NotFound('Repository not found: ' . $repositoryId);
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

    private function processForm(\Michcald\Dummy\App\Form\Model\Entity $form, $entity, $code = 201)
    {
        if ($form->isValid()) {

            $this->dao->persist($entity);

            $response = new \Michcald\Dummy\Response\Json();
            $response->setStatusCode($code)
                ->setContent($entity->getId());

            return $response;

        } else {

            $values = $form->getValues();

            $formErrors = array();
            foreach ($form->getElements() as $element) {
                $formErrors[$element->getName()] = array(
                    'value' => isset($values[$element->getName()]) ? $values[$element->getName()] : '',
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

    public function createAction($repositoryId)
    {
        $repository = $this->findRepository($repositoryId);

        if (!$repository) {
            return new \Michcald\Dummy\Response\Json\NotFound('Repository not found: ' . $repositoryId);
        }

        $this->dao->setRepository($repository);

        // find all the fields
        $query = new \Michcald\Dummy\Dao\Query();
        $query->addWhere('repository_id', $repository->getId())
            ->addOrder('display_order', 'ASC')
            ->setLimit(10000);
        $fields = $this->repositoryFieldDao->findAll($query);

        $form = new \Michcald\Dummy\App\Form\Model\Entity(
            $fields->getResults()
        );

        $form->setValues($this->getRequest()->getData());

        $entity = $this->dao->create($form->getValues());

        return $this->processForm($form, $entity);
    }

    public function updateAction($repositoryId, $id)
    {
        $repository = $this->findRepository($repositoryId);

        if (!$repository) {
            return new \Michcald\Dummy\Response\Json\NotFound('Repository not found: ' . $repositoryName);
        }

        $this->dao->setRepository($repository);

        // find all the fields
        $query = new \Michcald\Dummy\Dao\Query();
        $query->addWhere('repository_id', $repository->getId())
            ->addOrder('display_order', 'ASC')
            ->setLimit(10000);
        $fields = $this->repositoryFieldDao->findAll($query);

        $form = new \Michcald\Dummy\App\Form\Model\Entity(
            $fields->getResults()
        );

        $form->setValues($this->getRequest()->getData());

        $query = new \Michcald\Dummy\Dao\Query();
        $query->addWhere('id', $id);
        /* @var $entity \Michcald\Dummy\App\Model\Entity */
        $entity = $this->dao->findOne($query);

        if (!$entity) {
            return new \Michcald\Dummy\Response\Json\NotFound(sprintf('Entity not found: %d', $id));
        }

        $entityNew = $this->dao->create($form->getValues());

        $entityNew->setId($entity->getId());

        return $this->processForm($form, $entityNew, 204);
    }

    public function deleteAction($repositoryId, $id)
    {
        $repository = $this->findRepository($repositoryId);

        if (!$repository) {
            return new \Michcald\Dummy\Response\Json\NotFound('Repository not found: ' . $repositoryId);
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
