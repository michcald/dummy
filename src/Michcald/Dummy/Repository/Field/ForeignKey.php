<?php

namespace Michcald\Dummy\Repository\Field;

class ForeignKey extends \Michcald\Dummy\Repository\Field
{
    public function getDiscriminator()
    {
        return 'foreign_key';
    }
    
    public function toSQL()
    {
        return $this->getName() . ' INT(11) NOT NULL';
    }
    
    public function toArray()
    {
        $array = parent::toArray();
        
        $repositoryName = str_replace('_id', '', $this->getName());
        
        $registry = \Michcald\Dummy\RepositoryRegistry::getInstance();
        
        $repository = $registry->getRepository($repositoryName);
        
        $entities = $repository->findAll();
        
        foreach ($entities as $entity) {
            $array['options'][] = $entity->toExposeArray();
        }
        
        return $array;
    }
}