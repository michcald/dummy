<?php

namespace Michcald\Dummy\Controller;

use Michcald\Dummy\RepositoryRegistry;

class EntityController extends \Michcald\Mvc\Controller\CliController
{
    public function createAction()
    {
        $this->write('<cyan>Entity name:</cyan> ');
        $line = $this->readLine();
        
        $this->writeln($line);
        
        $this->writeln('<green>Installation completed!</green>');
        
        return new \Michcald\Mvc\Response();
    }
    
}