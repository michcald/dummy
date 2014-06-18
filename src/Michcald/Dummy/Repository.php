<?php

class repo{
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
        $registry = RepositoryRegistry::getInstance();
        
        foreach ($this->children as $child) {
            $r = $registry->getRepository($child);
            $childEntities = $r->findBy(array(
                $this->name . '_id' => (int)$entity->id
            ));
            foreach ($childEntities as $c) {
                $r->delete($c);
            }
        }

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
}
