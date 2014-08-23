<?php

namespace Michcald\Dummy\App\Controller\Administrable;

use Michcald\Dummy\Interfaces\Administrable;
use Michcald\Dummy\Controller\Crud;

use Michcald\Dummy\Response\Json as JsonResponse;

class GrantController extends Crud implements Administrable
{
    private $dao;

    public function __construct()
    {
        $this->dao = new \Michcald\Dummy\App\Dao\Grant();
    }

    public function createAction()
    {
        $response = new JsonResponse();
        $response->setStatusCode(501); // not implemented

        return $response;
    }

    public function readAction($id)
    {
        $query = new \Michcald\Dummy\Dao\Query();
        $query->addWhere('id', $id);

        $grant = $this->dao->findOne($query);

        if (!$grant) {
            return new \Michcald\Dummy\Response\Json\NotFound('Grant not found: ' . $id);
        }

        $response = new \Michcald\Dummy\Response\Json();

        $response->setContent($grant->toArray());

        return $response;
    }

    public function deleteAction($id)
    {
        $response = new JsonResponse();
        $response->setStatusCode(501); // not implemented

        return $response;
    }

    public function listAction()
    {
        $page = $this->getRequest()->getQueryParam('page', '1');
        $limit = $this->getRequest()->getQueryParam('limit', '30');
        $filters = $this->getRequest()->getQueryParam('filters', array());

        $form = new \Michcald\Dummy\App\Form\ListForm();
        $form->setFilters(array(
            'app_id',
        ));

        $form->setValues(array(
            'page' => $page,
            'limit' => $limit,
            'filters' => $filters,
            'orders' => array()
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

            foreach ($daoResult->getResults() as $grant) {
                $array['results'][] = $grant->toArray();
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

    public function updateAction($id)
    {

        $query = new \Michcald\Dummy\Dao\Query();
        $query->addWhere('id', $id);

        $grant = $this->dao->findOne($query);

        if (!$grant) {
            return new \Michcald\Dummy\Response\Json\NotFound('Grant not found: ' . $id);
        }

        $form = new \Michcald\Dummy\App\Form\Model\Grant();

        $form->setValues($this->getRequest()->getData());

        if ($form->isValid()) {

            $values = $form->getValues();

            $updatedGrant = $this->dao->create($values);
            $updatedGrant->setId($grant->getId());

            $this->dao->persist($updatedGrant);

            $response = new \Michcald\Dummy\Response\Json();
            $response->setStatusCode(204);

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

}
