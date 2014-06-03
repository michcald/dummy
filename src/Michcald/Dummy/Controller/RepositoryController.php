<?php

namespace Michcald\Dummy\Controller;

use Michcald\Dummy\Response\JsonResponse;
use Michcald\Dummy\Response\Error\NotFoundResponse;
use Michcald\Dummy\Response\Error\NotAuthorizedResponse;
use Michcald\Dummy\Response\Error\InternalResponse as InternalErrorResponse;

class RepositoryController extends \Michcald\Mvc\Controller\HttpController
{
    private function auth()
    {
        $user = $this->getRequest()->getHeader('PHP_AUTH_USER');
        $password = $this->getRequest()->getHeader('PHP_AUTH_PW');
        
        if (!$user || $user != 'stefano') {
            return false;
        }
        
        if ($password == '123456') {
            return true;
        }
        
        return false;
    }
    
    private function getRepositoryClassName($repository)
    {
        $tmp = \Michcald\Dummy\Util\String::underscoresToCamelCase($repository, true);
        
        return "\\App\\Repository\\" . $tmp;
    }
    
    public function infoAction($repository)
    {
        if (!$this->auth()) {
            return new NotAuthorizedResponse();
        }
        
        $response = new JsonResponse();
        
        $repoClass = $this->getRepositoryClassName($repository);

        if (!class_exists($repoClass)) {
            return new NotFoundResponse('Repository not found');
        }

        $repo = new $repoClass;

        $response->setContent($repo->toArray());

        return $response;
    }

    public function listAction($repository)
    {
        if (!$this->auth()) {
            return new NotAuthorizedResponse();
        }
        
        $page = (int) $this->getRequest()->getQueryParam('page', 1);
        $limit = (int) $this->getRequest()->getQueryParam('limit', 10);

        $query = $this->getRequest()->getQueryParam('query', false);

        $sortField = $this->getRequest()->getQueryParam('sfield', 'id');
        $sortDir = $this->getRequest()->getQueryParam('sdir', 'desc');

        //
        
        $response = new JsonResponse();

        $repoClass = $this->getRepositoryClassName($repository);

        if (!class_exists($repoClass)) {
            return new NotFoundResponse('Repository not found');
        }

        $repo = new $repoClass;
        
        $paginator = new \Michcald\Paginator();
        $paginator->setItemsPerPage($limit)
                ->setCurrentPageNumber($page);
        
        $order = $sortField . ' ' . $sortDir;
        
        $total = $repo->countBy(
                array(),
                $query // like
        );
        
        $entities = $repo->findBy(
                array(),
                $query, // like
                $order,
                $paginator->getLimit(), 
                $paginator->getOffset()
        );
        
        $array = array(
            'paginator' => array(
                'page' => $page,
                'total' => $total
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
        if (!$this->auth()) {
            return new NotAuthorizedResponse();
        }
        
        $response = new JsonResponse();

        $repoClass = $this->getRepositoryClassName($repository);

        if (!class_exists($repoClass)) {
            return new NotFoundResponse('Repository not found');
        }

        $repo = new $repoClass;

        $entity = $repo->findOne($id);

        if (!$entity) {
            return new NotFoundResponse('Entity not found');
        }

        $response->setContent($entity->toExposeArray());

        return $response;
    }

    public function createAction($repository)
    {
        if (!$this->auth()) {
            return new NotAuthorizedResponse();
        }
        
        $response = new JsonResponse();

        $repoClass = $this->getRepositoryClassName($repository);

        if (!class_exists($repoClass)) {
            return new NotFoundResponse('Repository not found');
        }

        $repo = new $repoClass;
        
        $data = $this->getRequest()->getData();
        
        $entity = $repo->create($data);
        
        if (!$entity) {
            return new InternalErrorResponse('Cannot create entity');
        }
        
        $id = $repo->persist($entity);
        
        if (!$id) {
            return new InternalErrorResponse('Cannot persist entity');
        }
        
        return $this->readAction($repository, $id);
    }

    public function updateAction($repository, $id)
    {
        if (!$this->auth()) {
            return new NotAuthorizedResponse();
        }
        
        $response = new JsonResponse();

        $repoClass = $this->getRepositoryClassName($repository);

        if (!class_exists($repoClass)) {
            return new NotFoundResponse('Repository not found');
        }

        $repo = new $repoClass;
        
        $entity = $repo->findOne($id);

        if (!$entity) {
            return new NotFoundResponse('Entity not found');
        }
        
        $data = $this->getRequest()->getData();
        
        foreach ($data as $key => $value) {
            $entity->$key = $value;
        }
        
        $id = $repo->persist($entity);
        
        if (!$id) {
            return new InternalErrorResponse('Cannot persist entity');
        }
        
        return $this->readAction($repository, $id);
    }

    public function deleteAction($repository, $id)
    {
        if (!$this->auth()) {
            return new NotAuthorizedResponse();
        }
        
        $response = new JsonResponse();

        $repoClass = $this->getRepositoryClassName($repository);

        if (!class_exists($repoClass)) {
            return new NotFoundResponse('Repository not found');
        }

        $repo = new $repoClass;
        
        $entity = $repo->findOne($id);

        if (!$entity) {
            return new NotFoundResponse('Entity not found');
        }
        
        $repo->delete($entity);

        return $response;
    }

    public function errorAction($any)
    {
        if (!$this->auth()) {
            return new NotAuthorizedResponse();
        }
        
        // may be better change type of response
        return new InternalErrorResponse('No routes found');
    }
}
