<?php

namespace Michcald\Dummy\App\Dao\Repository;

class Field extends \Michcald\Dummy\Dao
{
    public function create(array $row = null)
    {
        $field = new \Michcald\Dummy\App\Model\Repository\Field();

        if ($row) {
            $field
                ->setRepositoryId($row['repository_id'])
                ->setType($row['type'])
                ->setName($row['name'])
                ->setLabel($row['label'])
                ->setDescription($row['description'])
                ->setRequired((bool) $row['required'])
                ->setMain((bool) $row['main'])
                ->setSearchable((bool) $row['searchable'])
                ->setList((bool) $row['list'])
                ->setSortable((bool) $row['sortable'])
                ->setDisplayOrder($row['display_order'])
                ;

            if (isset($row['id'])) {
                $field->setId($row['id']);
            }
        }

        return $field;
    }

    public function persist($model)
    {
        /* @var $model \Michcald\Dummy\App\Model\Repository\Field */
        $db = $this->getDb();

        $stm = $db->prepare('SELECT name FROM meta_repository WHERE id=:id LIMIT 1');
        $stm->execute(array(
            'id' => $model->getRepositoryId()
        ));

        $repository = $stm->fetch(\PDO::FETCH_ASSOC);

        // check if the column already exist
        $stm = $db->prepare(sprintf('SHOW COLUMNS FROM %s WHERE Field=:field', $repository['name']));
        $stm->execute(array(
            'field' => $model->getName()
        ));

        $field = $stm->fetch(\PDO::FETCH_ASSOC);

        $db->beginTransaction();

        parent::persist($model);

        $type = null;
        switch ($model->getType()) {
            case 'string':
            case 'file':
                $type = 'VARCHAR(255)';
                break;
            case 'text':
                $type = 'TEXT';
                break;
            case 'integer':
            case 'foreign':
                $type = 'INT(11)';
                break;
            case 'float':
                $type = 'FLOAT(5)';
                break;
            case 'boolean':
                $type = 'INT(1)';
                break;
            case 'timestamp':
                $type = 'TIMESTAMP';
                break;
            default:
                throw new \Exception(sprintf('Invalid type: %s', $model->getType()));
        }

        if ($field) {

            if (strtolower($field['Type']) != strtolower($type)) {
                $db->exec(sprintf(
                    'ALTER TABLE %s MODIFY %s %s %s',
                    $repository['name'],
                    $model->getName(),
                    $type,
                    $model->isRequired() ? 'NOT NULL' : 'NULL'
                ));
            }

            // @TODO manager foreign

        } else {
            $db->exec(sprintf(
                'ALTER TABLE %s ADD %s %s %s',
                $repository['name'],
                $model->getName(),
                $type,
                $model->isRequired() ? 'NOT NULL' : 'NULL'
            ));

            if ($model->getType() == 'foreign') {
                $db->exec(sprintf(
                    'ALTER TABLE %s ADD FOREIGN KEY (%s) REFERENCES %s(%s) %s',
                    $repository['name'],
                    $model->getName(),
                    $model->getName(),
                    'id',
                    $model->isRequired() ? 'ON DELETE CASCADE' : ''
                ));
            }

        }

        $db->commit();
    }

    public function delete($model)
    {
        /* @var $model \Michcald\Dummy\App\Model\Repository\Field */
        $db = $this->getDb();

        $stm = $db->prepare('SELECT name FROM meta_repository WHERE id=:id LIMIT 1');
        $stm->execute(array(
            'id' => $model->getRepositoryId()
        ));

        $repository = $stm->fetch(\PDO::FETCH_ASSOC);

        $db->beginTransaction();

        parent::delete($model);

        if ($model->getType() == 'foreign') {

            // retrieve foreign key name
            $stm = $db->prepare('select TABLE_NAME,COLUMN_NAME,CONSTRAINT_NAME,REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME '
                . 'from INFORMATION_SCHEMA.KEY_COLUMN_USAGE '
                . 'where TABLE_SCHEMA = :dbname '
                . 'and TABLE_NAME = :table '
                . 'and COLUMN_NAME = :column '
                . 'and REFERENCED_TABLE_NAME = :referencedTable '
                . 'and REFERENCED_COLUMN_NAME = :referencedColumn '
                . 'limit 1'
            );
            $stm->execute(array(
                'dbname' => \Michcald\Dummy\Config::getInstance()->database['dbname'],
                'table'  => $repository['name'],
                'column' => $model->getName(),
                'referencedTable' => $model->getName(),
                'referencedColumn' => 'id'
            ));

            $foreignKey = $stm->fetch(\PDO::FETCH_ASSOC);

            $db->exec(sprintf(
                'ALTER TABLE %s DROP FOREIGN KEY %s',
                $repository['name'],
                $foreignKey['CONSTRAINT_NAME']
            ));
        }

        $db->exec(sprintf(
            'ALTER TABLE %s DROP %s',
            $repository['name'],
            $model->getName()
        ));

        $db->commit();
    }

    public function getTable()
    {
        return 'meta_repository_field';
    }

}