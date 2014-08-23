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
        $query = $this->getRequest()->getQueryParam('query', '');
        $filters = $this->getRequest()->getQueryParam('filters', array());
        $orders = $this->getRequest()->getQueryParam('orders', array());

        $form = new \Michcald\Dummy\App\Form\ListForm();

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

        if ($form->isValid()) {
            
            $entity = $this->dao->create($form->getValues());

            $this->dao->persist($entity);

            $response = new \Michcald\Dummy\Response\Json();
            $response->setStatusCode(201)
                ->setContent($entity->getId());

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
