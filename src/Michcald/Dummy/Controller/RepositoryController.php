<?php

namespace Michcald\Dummy\Controller;

use Michcald\Dummy\Response\JsonResponse;
use Michcald\Dummy\Response\Error\NotFoundResponse;
use Michcald\Dummy\Response\Error\InternalResponse as InternalErrorResponse;

use Michcald\Dummy\RepositoryRegistry;

class RepositoryController extends \Michcald\Mvc\Controller\HttpController
{
    public function infoAction($repository)
    {
        $response = new JsonResponse();
        
        $repo = null;
        try {
            $repo = RepositoryRegistry::getInstance()->getRepository($repository);
        } catch (\Exception $e) {
            return new NotFoundResponse('Repository not found: ' . $repository);
        }

        $response->setContent($repo->toArray());

        return $response;
    }

    public function listAction($repository)
    {
        $page = (int) $this->getRequest()->getQueryParam('page', 1);
        $limit = (int) $this->getRequest()->getQueryParam('limit', 10);

        $query = $this->getRequest()->getQueryParam('query', false);

        $sortField = $this->getRequest()->getQueryParam('orderb', null);
        $sortDir = $this->getRequest()->getQueryParam('orderd', null);

        //
        
        $response = new JsonResponse();

        $repo = null;
        try {
            $repo = RepositoryRegistry::getInstance()->getRepository($repository);
        } catch (\Exception $e) {
            return new NotFoundResponse('Repository not found: ' . $repository);
        }
        
        $paginator = new \Michcald\Paginator();
        $paginator->setItemsPerPage($limit)
                ->setCurrentPageNumber($page);
        
        $order = null;
        if ($sortField) {
            $order = $sortField;
            if ($sortDir) {
                $order .= ' ' . $sortDir;
            }
        }
        
        
        $total = $repo->countBy(
                array(),
                $query // like
        );
        
        $paginator->setTotalItems($total);
        
        $entities = $repo->findBy(
                array(),
                $query, // like
                $order,
                $paginator->getLimit(), 
                $paginator->getOffset()
        );
        
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
        
        foreach ($entities as $entity) {
            $array['results'][] = $entity->toExposeArray();
        }
        
        $response->setContent($array);

        return $response;
    }

    public function readAction($repository, $id)
    {
        $response = new JsonResponse();

        $repo = null;
        try {
            $repo = RepositoryRegistry::getInstance()->getRepository($repository);
        } catch (\Exception $e) {
            return new NotFoundResponse('Repository not found: ' . $repository);
        }

        $entity = $repo->findOne($id);

        if (!$entity) {
            return new NotFoundResponse('Entity not found');
        }

        $response->setContent($entity->toExposeArray());

        return $response;
    }

    public function createAction($repository)
    {
        $response = new JsonResponse();

        $repo = null;
        try {
            $repo = RepositoryRegistry::getInstance()->getRepository($repository);
        } catch (\Exception $e) {
            return new NotFoundResponse('Repository not found: ' . $repository);
        }
        
        $data = $this->getRequest()->getData();
        
        $entity = $repo->create($data);
        
        if (!$repo->validate($entity)) {
            $response->setStatusCode(400);
            $response->setContent(array(
                'error' => array(
                    'status_code' => $response->getStatusCode(),
                    'message'     => 'Missing fields',
                    'fields'      => $repo->getValidationErrors()
                )
            ));
            return $response;
        }
        
        $id = $repo->persist($entity);
        
        if (!$id) {
            return new InternalErrorResponse('Cannot persist entity');
        }
        
        $response->setStatusCode(201);
        
        return $response;
    }

    public function updateAction($repository, $id)
    {
        $response = new JsonResponse();

        $repo = null;
        try {
            $repo = RepositoryRegistry::getInstance()->getRepository($repository);
        } catch (\Exception $e) {
            return new NotFoundResponse('Repository not found: ' . $repository);
        }
        
        $entity = $repo->findOne($id);

        if (!$entity) {
            return new NotFoundResponse('Entity not found');
        }
        
        $data = $this->getRequest()->getData();
        
        foreach ($data as $key => $value) {
            $entity->$key = $value;
        }
        
        $id = $repo->persist($entity);
        
        // 400 missing field
        
        if (!$id) {
            return new InternalErrorResponse('Cannot persist entity');
        }
        
        $response->setStatusCode(204); // no content
        
        return $response;
    }

    public function deleteAction($repository, $id)
    {
        $response = new JsonResponse();

        $repo = null;
        try {
            $repo = RepositoryRegistry::getInstance()->getRepository($repository);
        } catch (\Exception $e) {
            return new NotFoundResponse('Repository not found: ' . $repository);
        }
        
        $entity = $repo->findOne($id);

        if (!$entity) {
            return new NotFoundResponse('Entity not found');
        }
        
        try {
            $repo->delete($entity);
        } catch (\Exception $e) {
            // 500 cannot delete
            $response = new InternalErrorResponse();
            $response->setMessage('Cannot delete the entity');
            return $response;
        }
        
        $response->setStatusCode(204); // no content
        
        return $response;
    }
}
