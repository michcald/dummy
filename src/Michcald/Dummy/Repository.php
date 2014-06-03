<?php

namespace Michcald\Dummy;

abstract class Repository
{
    private $fields = array();

    public function __construct()
    {
        $id = new Entity\Field\Integer('id');
        $id->setExpose(true)
            ->setSearchable(false);
        // add validation
        $this->addField($id);

        foreach ($this->getParentEntities() as $entity) {
            $foreignKey = new Entity\Field\Integer($entity . '_id');
            $foreignKey
                ->setLabel('')
                ->setDescription('')
                ->setExpose(true)
                ->setSearchable(false);
            $this->addField($foreignKey);
        }
    }

    protected function addField(Entity\Field $field)
    {
        $this->fields[] = $field;

        return $this;
    }

    public function getFields()
    {
        return $this->fields;
    }

    abstract public function getName();

    abstract public function getDb();

    public function getDescription()
    {
        return '';
    }

    abstract public function getLabel($plural = false);

    abstract public function getMaxRecords();

    public function getParentEntities()
    {
        return array();
    }

    public function getChildEntitites()
    {
        return array();
    }

    public function count()
    {
        return $this->getDb()->fetchOne(
                'SELECT COUNT(id) FROM ' . $this->getName());
    }

    private function validate(Entity $entity)
    {
        foreach ($this->fields as $field) {

            $fieldName = $field->getName();

            if ($fieldName == 'id') {
                continue;
            }

            if (!$field->validate($entity->$fieldName)) {
                return false;
            }
        }

        return true;
    }

    public function create(array $data = null)
    {
        $entity = new Entity($this);

        if (is_array($data)) {
            foreach ($this->fields as $field) {
                $fieldName = $field->getName();
                if (array_key_exists($field->getName(), $data)) {
                    $entity->$fieldName = $data[$fieldName];
                    // TODO gestire tipo file
                } else {
                    if ($field->isRequired()) {
                        throw new \Exception('Field required: ' . $fieldName);
                    }
                }
            }
        }
        
        return $entity;
    }

    public function findOne($id)
    {
        $row = $this->getDb()->fetchRow(
            'SELECT * FROM ' . $this->getName() . ' WHERE id=?', $id);

        if (!$row) {
            return false;
        }

        $entity = $this->create();

        foreach ($row as $key => $value) {
            $entity->$key = $value;
        }

        return $entity;
    }
    
    public function findAll($order, $limit, $offset)
    {
        $rows = $this->getDb()->fetchAll(
            'SELECT * FROM ' . $this->getName() . ' ORDER BY ' . $order
                . ' LIMIT ' . $limit . ' OFFSET ' . $offset
        );
        
        $entities = array();
        
        foreach ($rows as $row) {
            $entity = $this->create();
            foreach ($row as $key => $value) {
                $entity->$key = $value;
            }
            $entities[] = $entity;
        }
        
        return $entities;
    }
    
    public function findBy(array $where, $query, $order, $limit, $offset)
    {
        $sql = 'SELECT * FROM ' . $this->getName() . ' WHERE ';
        
        $tmp = array(
            '1' => '1'
        );
        foreach ($where as $key => $value) {
            $tmp[] = $key . '="' . $value . '"';
        }
        
        $tmp2 = array();
        if ($query && strlen($query) > 2) {
            foreach ($this->fields as $field) {
                if ($field->isSearchable()) {
                    $tmp2[] = $field->getName() . ' LIKE "%' . $query . '%"';
                }
            }
        }

        $sql .= implode(' AND ', $tmp);
        
        if (count($tmp2) > 0) {
            $sql .= ' AND (' . implode(' OR ', $tmp2) . ')';
        }
        
        if ($order) {
            $sql .= ' ORDER BY ' . $order;
        }
        
        $sql .= ' LIMIT ' . $limit  . ' OFFSET ' . $offset;
        
        $rows = $this->getDb()->fetchAll($sql);
        
        $entities = array();
        
        foreach ($rows as $row) {
            $entity = $this->create();
            foreach ($row as $key => $value) {
                $entity->$key = $value;
            }
            $entities[] = $entity;
        }
        
        return $entities;
    }
    
    public function countBy(array $where, $query)
    {
        $sql = 'SELECT COUNT(id) FROM ' . $this->getName() . ' WHERE ';
        
        $tmp = array(
            '1' => '1'
        );
        foreach ($where as $key => $value) {
            $tmp[] = $key . '="' . $value . '"';
        }
        
        $tmp2 = array();
        if ($query && strlen($query) > 2) {
            foreach ($this->fields as $field) {
                if ($field->isSearchable()) {
                    $tmp2[] = $field->getName() . ' LIKE "%' . $query . '%"';
                }
            }
        }

        $sql .= implode(' AND ', $tmp);
        
        if (count($tmp2) > 0) {
            $sql .= ' AND (' . implode(' OR ', $tmp2) . ')';
        }
        
        return $this->getDb()->fetchOne($sql);
    }

    public function persist(Entity $entity)
    {
        if (!$this->validate($entity)) {
            return false;
        }
        
        $array = $entity->toArray(false);
        $toSaveArray = $array;
        
        foreach ($this->fields as $field) {
            if ($field instanceof Entity\Field\File &&
                    is_array($array[$field->getName()])) {
                
                $filename = $toSaveArray[$field->getName()]['name'];
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                
                $tmp = $field->getName() . time() . rand(0, 1000);                
                
                $newName = md5($tmp) . '.' . $ext;
                
                $toSaveArray[$field->getName()] = $newName;
            }
        }
        
        if (!$entity->id) {
            if ($this->getMaxRecords() && $this->count() < $this->getMaxRecords()) {
                $id = $this->getDb()->insert(
                    $this->getName(),
                    $toSaveArray
                );
                $entity->id = $id;
            } else {
                return false;
            }
        } else {
            $this->getDb()->update(
                $this->getName(),
                $toSaveArray,
                'id=' . $entity->id
            );
        }
        
        if (!is_dir('uploads/' . $this->getName())) {
            mkdir('uploads/' . $this->getName());
        }
        
        $dir = 'uploads/' . $this->getName() . '/' . $entity->id;
        if (!is_dir($dir)) {
            mkdir($dir);
        }
        
        // verify if there's a file to save
        foreach ($this->fields as $field) {
            if ($field instanceof Entity\Field\File && 
                    is_array($array[$field->getName()])) {
                $fieldName = $field->getName();
                
                $newName = $toSaveArray[$fieldName];
                $tmpName = $array[$fieldName]['tmp_name'];
                
                move_uploaded_file(
                    $tmpName, 
                    $dir . '/' . $newName
                );
            }
        }
        
        return $entity->id;
    }
    
    public function delete(Entity $entity)
    {
        $this->getDb()->delete(
            $this->getName(),
            'id=' . (int)$entity->id
        );
        
        $dir = 'uploads/' . $this->getName() . '/' . $entity->id;
        if (is_dir($dir)) {
            $this->delTree($dir);
        }
    }
    
    private function delTree($dir)
    {
        $files = array_diff(scandir($dir), array('.','..')); 
        foreach ($files as $file) { 
            (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file"); 
        }
        return rmdir($dir); 
    }

    public function toArray()
    {
        $array = array(
            'name' => $this->getName(),
            'label' => array(
                'singular' => $this->getLabel(),
                'plural' => $this->getLabel(true)
            ),
            'description' => $this->getDescription(),
            'max_records' => $this->getMaxRecords(),
            'parents' => $this->getParentEntities(),
            'children' => $this->getChildEntitites(),
            'fields' => array()
        );

        foreach ($this->fields as $field) {
            $array['fields'][] = $field->toArray();
        }

        return $array;
    }
}
