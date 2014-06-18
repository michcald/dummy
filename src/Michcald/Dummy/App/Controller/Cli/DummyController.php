<?php

namespace Michcald\Dummy\Controller\Cli;

use Michcald\Dummy\RepositoryRegistry;

class DummyController extends \Michcald\Mvc\Controller\CliController
{
    public function createAction()
    {
        $config = \Michcald\Dummy\Config::getInstance();
        
        if (!is_dir($config->path['repositories'])) {
            mkdir($config->path['repositories'], 0777);
        }
        
        $this->writeln('<yellow>Creation of a new repository</yellow>');
        
        $name = null;
        do {
            $this->write('<cyan>Entity name:</cyan> ');
            $name = $this->readLine();
        } while (!$name);
        
        $this->write('<cyan>Entity description [ENTER for skipping]:</cyan> ');
        $description = $this->readLine();
        
        $singularLabel = null;
        do {
            $this->write('<cyan>Entity label (singular):</cyan> ');
            $singularLabel = $this->readLine();
        } while (!$singularLabel);
        
        $pluralLabel = null;
        do {
            $this->write('<cyan>Entity label (plural):</cyan> ');
            $pluralLabel = $this->readLine();
        } while (!$pluralLabel);
        
        $parents = array();
        do {
            $this->write('<cyan>Entity parents (ENTER when done):</cyan> ');
            $parent = $this->readLine();
            if ($parent) {
                $parents[] = $parent;
            }
        } while ($parent);
        
        $children = array();
        do {
            $this->write('<cyan>Entity children (ENTER when done):</cyan> ');
            $child = $this->readLine();
            if ($child) {
                $children[] = $child;
            }
        } while ($child);
        
        
        $this->writeln('<green>Installation completed!</green>');
        
        return new \Michcald\Mvc\Response();
    }
    
    public function deleteAction()
    {
        $this->writeln('<yellow>Deletion of a repository</yellow>');
        
        return new \Michcald\Mvc\Response();
    }
    
    public function infoAction()
    {
        $this->writeln('<yellow>List of repositories</yellow>');
        
        $registry = RepositoryRegistry::getInstance();
        
        foreach ($registry->getRepositories() as $repository) {
            $this->writeln('<cyan>' . $repository->getName() . '</cyan>');
            
            $this->writeln("\t" . '<light-red>Description:</light-red> ' . $repository->getDescription());
            
            $this->writeln("\t" . '<light-red>Label:</light-red> ' . $repository->getSingularLabel() . ' (' . $repository->getPLuralLabel() . ')');
            
            $this->writeln("\t" . '<light-red>Parents:</light-red>');
            foreach ($repository->getParents() as $parent) {
                $this->writeln("\t\t" . $parent);
            }
            
            $this->writeln("\t" . '<light-red>Children:</light-red>');
            foreach ($repository->getChildren() as $child) {
                $this->writeln("\t\t" . $child);
            }
            
            $this->writeln("\t" . '<light-red>Fields:</light-red>');
            foreach ($repository->getFields() as $field) {
                $this->write('<light-gray>');
                $this->write("\t\t" . $field->getName());
                $this->write('</light-gray>');
                $this->write(' ');
                $this->write("\t" . '<brown>label=</brown>' . $field->getLabel() . "\t");
                $this->write('<brown>description=</brown>' . $field->getDescription());
                $this->writeln(' ');
                $this->write("\t\t\t" . '<brown>');
                $this->write('<brown>type=</brown>' . $field->getDiscriminator() . "\t");
                $this->write('<brown>searchable=</brown>' . $field->isSearchable() . "\t");
                $this->write('<brown>required=</brown>' . $field->isRequired());
                $this->writeln(''); // fix in mvc repo
            }
            
            $this->readLine();
        }
        
        
        
        return new \Michcald\Mvc\Response();
    }
}