<?php

namespace Michcald\Dummy\App\Controller;

use Michcald\Dummy\Controller\Crud;

use Michcald\Dummy\Response\Json as JsonResponse;

class AppController extends Crud
{
    private $dao;

    public function __construct()
    {
        $this->dao = new \Michcald\Dummy\App\Dao\App();
    }

    public function whoAmIAction()
    {
        $app = $this->getApp();

        $grantDao = new \Michcald\Dummy\App\Dao\Grant();
        $repositoryDao = new \Michcald\Dummy\App\Dao\Repository();

        $query = new \Michcald\Dummy\Dao\Query();
        $query->addWhere('app_id', $app->getId())
            ->setLimit(1000);

        $grants = $grantDao->findAll($query);

        $array = $app->toArray();

        $array['grants'] = array();
        foreach ($grants->getResults() as $grant) {
            /* @var $grant \Michcald\Dummy\App\Model\Grant */

            /* @var $repository \Michcald\Dummy\App\Model\Repository */
            $q = new \Michcald\Dummy\Dao\Query();
            $q->addWhere('id', $grant->getRepositoryId());
            $repository = $repositoryDao->findOne($q);

            $array['grants'][] = array(
                'repository' => $repository->toArray(),
                'create' => $grant->getCreate(),
                'read' => $grant->getRead(),
                'update' => $grant->getUpdate(),
                'delete' => $grant->getDelete(),
            );
        }

        $response = new \Michcald\Dummy\Response\Json();

        $response->setContent($array);

        return $response;
    }

}
