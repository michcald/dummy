<?php

namespace Michcald\Dummy\Controller;

abstract class Crud extends \Michcald\Mvc\Controller\HttpController
{
    public function createAction() {}

    public function readAction($id) {}

    public function listAction() {}

    public function updateAction($id) {}

    public function deleteAction($id) {}

    /**
     * @return \Michcald\Dummy\App\Model\App
     */
    protected function getApp()
    {
        return  \Michcald\Mvc\Container::get('dummy.app');
    }
}