<?php

namespace Michcald\Dummy\Controller;

use Michcald\Dummy\Response\JsonResponse;
use Michcald\Dummy\Response\Error\NotFoundResponse;
use Michcald\Dummy\Response\Error\InternalResponse as InternalErrorResponse;

use Michcald\Dummy\RepositoryRegistry;

class DummyController extends \Michcald\Mvc\Controller\HttpController
{
    public function infoAction()
    {
        $registry = RepositoryRegistry::getInstance();
        
        $repos = array();
        
        foreach ($registry->getRepositories() as $repository) {
            $repos[] = $repository->toConfigArray();
        }
        
        $response = new JsonResponse();
        
        $response->setContent($repos);
        
        return $response;
    }
}
