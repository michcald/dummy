<?php

namespace Michcald\Dummy;

class Repository
{
    private $name;
    
    private $description;
    
    private $singularLabel;
    
    private $pluralLabel;
    
    private $fields = array();
    
    private $parents = array();
    
    private $children = array();
    
    private $showable = false;

    public function __construct($name)
    {
        $this->name = $name;
        
        $id = new Repository\Field\PrimaryKey('id');
        $this->addField($id);
    }

    public function addField(Repository\Field $field)
    {
        $this->fields[$field->getName()] = $field;

        return $this;
    }

    public function getFields()
    {
        return $this->fields;
    }
    
    public function hasField($name)
    {
        return array_key_exists($name, $this->fields);
    }

    public function setName($name)
    {
        $this->name = $name;
        
        return $this;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getDb()
    {
        return \Michcald\Mvc\Container::get('dummy.db');
    }
    
    public function setDescription($description)
    {
        $this->description = $description;
        
        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setSingularLabel($singularLabel)
    {
        $this->singularLabel = $singularLabel;
        
        return $this;
    }
    
    public function getSingularLabel()
    {
        return $this->singularLabel;
    }
    
    public function setPluralLabel($pluralLabel)
    {
        $this->pluralLabel = $pluralLabel;
        
        return $this;
    }
    
    public function getPluralLabel()
    {
        return $this->pluralLabel;
    }

    public function addParent($parent)
    {
        $this->parents[] = $parent;
        
        $name = $parent . '_id';
        
        $field = new Repository\Field\ForeignKey($name);
        $field->setLabel($parent)
                ->setRequired(true);
        
        $this->addField($field);
        
        return $this;
    }
    
    public function getParents()
    {
        return $this->parents;
    }

    public function addChild($child)
    {
        $this->children[] = $child;
        
        return $this;
    }
    
    public function getChildren()
    {
        return $this->children;
    }
    
    public function setShowable($showable)
    {
        $this->showable = $showable;
        
        return $this;
    }
    
    public function isShowable()
    {
        return $this->showable;
    }
    
    public function getMainField()
    {
        foreach ($this->fields as $field) {
            if ($field->isMain()) {
                return $field;
            }
        }
        
        return null;
    }

    public function count()
    {
        return $this->getDb()->fetchOne(
                'SELECT COUNT(id) FROM ' . $this->getName());
    }

    public function validate(Entity $entity)
    {
        $validated = true;
        
        foreach ($this->fields as $field) {

            $fieldName = $field->getName();

            if ($fieldName instanceof Repository\Field\PrimaryKey) {
                continue;
            }

            if (!$field->validate($entity->$fieldName)) {
                $validated = false;
            }
        }

        return $validated;
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
    
    public function findAll($order = null, $limit = null, $offset = null)
    {
        $sql = 'SELECT * FROM ' . $this->getName();
        
        if ($order) {
            $sql .= ' ORDER BY ' . $order;
        }
        
        if ($limit) {
            $sql .= ' LIMIT ' . $limit;
        }
        
        if ($offset) {
            $sql .= ' OFFSET ' . $offset;
        }
        
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
    
    public function findBy(array $where = array(), $query = null, $order = null, $limit = null, $offset = null)
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
        
        if ($limit) {
            $sql .= ' LIMIT ' . $limit;
        }
        
        if ($offset) {
            $sql .= ' OFFSET ' . $offset;
        }
        
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

    public function getValidationErrors()
    {
        $errors = array();
        foreach ($this->fields as $field) {
            $errors[$field->getName()] = $field->getValidationErrors();
        }
        
        return $errors;
    }
    
    public function persist(Entity $entity)
    {
        if (!$this->validate($entity)) {
            return false;
        }
        
        $array = $entity->toArray(false);
        $toSaveArray = $array;
        
        foreach ($this->fields as $field) {
            if ($field instanceof Repository\Field\File &&
                    isset($array[$field->getName()]['tmp_name'])) {
                
                $filename = $toSaveArray[$field->getName()]['name'];
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                
                $tmp = $field->getName() . time() . rand(0, 1000);                
                
                $newName = md5($tmp) . '.' . $ext;
                
                $toSaveArray[$field->getName()] = $newName;
            }
        }
        
        if (!$entity->id) {
            $id = $this->getDb()->insert(
                $this->getName(),
                $toSaveArray
            );
            $entity->id = $id;
        } else {
            $this->getDb()->update(
                $this->getName(),
                $toSaveArray,
                'id=' . $entity->id
            );
        }
        
        $config = Config::getInstance();
        
        if (!is_dir($config->dir['uploads'] . '/' . $this->getName())) {
            mkdir($config->dir['uploads'] . '/' . $this->getName());
        }
        
        $dir = $config->dir['uploads'] . '/' . $this->getName() . '/' . $entity->id;
        if (!is_dir($dir)) {
            mkdir($dir);
        }
        
        // verify if there's a file to save
        foreach ($this->fields as $field) {
            if ($field instanceof Repository\Field\File && 
                    isset($array[$field->getName()]['tmp_name'])) {
                $fieldName = $field->getName();
                
                $newName = $toSaveArray[$fieldName];
                $tmpName = $array[$fieldName]['tmp_name'];
                //is_uploaded_file($dir);
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
        
        $config = Config::getInstance();
        
        $dir = $config->dir['uploads'] . '/' . $this->getName() . '/' . $entity->id;
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
                'singular' => $this->getSingularLabel(),
                'plural' => $this->getPluralLabel(true)
            ),
            'description' => $this->getDescription(),
            'parents' => $this->getParents(),
            'children' => $this->getChildren(),
            'showable' => $this->isShowable(),
            'fields' => array()
        );

        foreach ($this->fields as $field) {
            $array['fields'][] = $field->toArray();
        }

        return $array;
    }
    
    public function toConfigArray()
    {
        $config = array(
            'name'        => $this->getName(),
            'description' => $this->getDescription(),
            'label'       => array(
                'singular' => $this->getSingularLabel(),
                'plural'   => $this->getPluralLabel()
            ),
            'parents'     => $this->getParents(),
            'children'    => $this->getChildren(),
            'showable'    => $this->isShowable(),
            'fields'      => array()
        );
        
        foreach ($this->fields as $field) {
            $config['fields'][] = $field->toConfigArray();
        }
        
        return $config;
    }
}
