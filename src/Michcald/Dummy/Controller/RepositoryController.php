<?php

namespace Michcald\Dummy\Controller;

class RepositoryController extends \Michcald\Mvc\Controller\HttpController
{
    public function infoAction($repository)
    {
        $response = new \Michcald\Mvc\Response();
        $response->addHeader('Content-Type', 'application/json');

        $repoClass = "\\App\\Repository\\" . $this->dashesToCamelCase($repository, true);

        if (!class_exists($repoClass)) {
            $response->setStatusCode(500)
                    ->setContent(json_encode(array(
                        'error' => 'not valid repo'
                    )));
            return $response;
        }

        $repo = new $repoClass;

        $json = json_encode($repo->toArray());

        $response->setStatusCode(200)
                ->setContent($json);

        return $response;
    }

    public function listAction($repository)
    {
        $page = (int) $this->getRequest()->getQueryParam('page', 1);
        $limit = (int) $this->getRequest()->getQueryParam('limit', 10);

        $query = $this->getRequest()->getQueryParam('query', false);

        $sortField = $this->getRequest()->getQueryParam('sfield', 'id');
        $sortDir = $this->getRequest()->getQueryParam('sdir', 'desc');

        // get the parents for filtering

        $response = new \Michcald\Mvc\Response();
        $response->addHeader('Content-Type', 'application/json')
                ->setStatusCode(501)
                ->setContent(json_encode(array(
                    'err' => 'not implemented'
                )));

        return $response;
    }

    public function readAction($repository, $id)
    {
        $response = new \Michcald\Mvc\Response();
        $response->addHeader('Content-Type', 'application/json')
                ->setStatusCode(501)
                ->setContent(json_encode(array(
                    'err' => 'not implemented'
                )));

        return $response;
    }

    public function createAction($repository)
    {
        $response = new \Michcald\Mvc\Response();
        $response->addHeader('Content-Type', 'application/json')
                ->setStatusCode(501)
                ->setContent(json_encode(array(
                    'err' => 'not implemented'
                )));

        return $response;
    }

    public function updateAction($repository, $id)
    {
        $response = new \Michcald\Mvc\Response();
        $response->addHeader('Content-Type', 'application/json')
                ->setStatusCode(501)
                ->setContent(json_encode(array(
                    'err' => 'not implemented'
                )));

        return $response;
    }

    public function deleteAction($repository, $id)
    {
        $response = new \Michcald\Mvc\Response();
        $response->addHeader('Content-Type', 'application/json')
                ->setStatusCode(501)
                ->setContent(json_encode(array(
                    'err' => 'not implemented'
                )));

        return $response;
    }

    public function errorAction($any)
    {
        $response = new \Michcald\Mvc\Response();
        $response->addHeader('Content-Type', 'application/json')
                ->setStatusCode(500)
                ->setContent(json_encode(array(
                    'err' => 'not routes found'
                )));

        return $response;
    }

    private function dashesToCamelCase($string, $capitalizeFirstCharacter = false)
    {

        $str = str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));

        if (!$capitalizeFirstCharacter) {
            $str[0] = strtolower($str[0]);
        }

        return $str;
    }
}