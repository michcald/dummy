<?php

namespace Michcald\Dummy;

abstract class Controller extends \Michcald\Mvc\Controller\HttpController
{
    abstract public function createAction();

    abstract public function readAction($id);

    abstract public function listAction();

    abstract public function updateAction($id);

    abstract public function deleteAction($id);
}