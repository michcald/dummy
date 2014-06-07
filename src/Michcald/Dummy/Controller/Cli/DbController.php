<?php

namespace Michcald\Dummy\Controller\Cli;

use Michcald\Dummy\RepositoryRegistry;

class DbController extends \Michcald\Mvc\Controller\CliController
{
    public function installAction()
    {
        $registry = RepositoryRegistry::getInstance();
        
        $queries = array();
        
        foreach ($registry->getRepositories() as $repository) {
            
            $db = $repository->getDb();
            
            $tables = $db->fetchCol('SHOW TABLES');
            
            if (in_array($repository->getName(), $tables)) {
                
                $columns = $db->fetchAll('SHOW FULL COLUMNS FROM ' . $repository->getName());
                
                // dropping all the columns no longer mapped with the repo
                foreach ($columns as $column) {
                    if (!$repository->hasField($column['Field'])) {
                        $sql = 'ALTER TABLE ' . $repository->getName() 
                                . ' DROP COLUMN ' . $column['Field'];
                        $queries[] = $sql;
                    }
                }
                
                foreach ($repository->getFields() as $field) {
                    $found = 0;
                    foreach ($columns as $column) {
                        if ($column['Field'] == $field->getName()) {
                            $found = 1;
                            break;
                        }
                    }
                    
                    if ($found == 0) {
                        
                        $sql = 'ALTER TABLE ' . $repository->getName() . ' ADD COLUMN '
                                . $repository->getName() . ' ' . $field->toSQL();
                        
                        $queries[] = $sql;
                        
                    } else {
                        // verify the types etc
                    }
                }

            } else {
                $sql = 'CREATE TABLE ' . $repository->getName() . ' (';
                
                $tmp = array();
                foreach ($repository->getFields() as $field) {
                    $tmp[] = $field->toSQL();
                }
                
                $sql .= implode(', ', $tmp);
                
                $sql .= ')';
                
                $queries[] = $sql;
            }
        }
        
        foreach ($queries as $q) {
            $this->writeln('<green>' . $q . '</green>');
            $this->writeln('<yellow>Do you want to execute it?</yellow>');
            if ($this->confirm()) {
                $db->query($q);
                $this->writeln('<cyan>Done</cyan>');
            }
        }
        
        return new \Michcald\Mvc\Response();
    }
    
    public function dropAction()
    {
        $registry = RepositoryRegistry::getInstance();
        
        foreach ($registry->getRepositories() as $repository) {
            
            $db = $repository->getDb();
            
            $db->query('DROP TABLE IF EXISTS ' . $repository->getName());
        }
        
        $this->writeln('<green>Done</green>');
        
        return new \Michcald\Mvc\Response();
    }
}
