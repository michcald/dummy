<?php

namespace Michcald\Dummy\Dao;

class Query
{
    private $table;

    private $where = array();

    private $like = array();

    private $order = array();

    private $limit = 20;

    private $offset = 0;

    final public function setTable($table)
    {
        $this->table = $table;

        return $this;
    }

    final public function getTable()
    {
        return $this->table;
    }

    final public function addWhere($field, $value)
    {
        $this->where[$field] = $value;

        return $this;
    }

    final public function addLike(array $fields, $value)
    {
        foreach ($fields as $field) {
            $this->like['fields'] = $field;
        }

        $this->like['value'] = $value;

        return $this;
    }

    public function addOrder($field, $direction = 'ASC')
    {
        $this->order[$field] = $direction;

        return $this;
    }

    final public function setLimit($limit)
    {
        $this->limit = (int)$limit;

        return $this;
    }

    final public function setOffset($offset)
    {
        $this->offset = $offset;

        return $this->offset;
    }

    public function getWhereString()
    {
        $and = array();
        foreach ($this->where as $field => $value) {
            $and[] = '`' . $field . '`="' . addslashes($value) . '"';
        }

        // putting together the likes
        $or = array();
        foreach ($this->likes['fields'] as $field) {
            $or[] = '`' . $field . '` LIKE "%' . addslashes($value) . '%"';
        }

        $and[] = '(' . implode(' OR ', $or) . ')';

        return implode(' AND ', $and);
    }

    public function getOrderString()
    {
        $str = array();
        foreach ($this->order as $field => $value) {
            $str[] = '`' . $field . '` ' . $value;
        }

        return implode(', ', $str);
    }

    private function getQuery()
    {
        $sql = 'FROM ' . $this->table;

        $where = null;

        if (count($this->where) > 0 || count($this->like['fields']) > 0) {
            $where .= ' WHERE ' . $this->getWhereString();
        }

        $sql .= $where;

        if (count($this->order)) {
            $sql .= ' ORDER BY ' . $this->getOrderString();
        }

        $sql .= ' LIMIT ' . $this->limit . ' OFFSET ' . $this->offset;

        return $sql;
    }

    final public function getSelectQuery()
    {
        return 'SELECT * ' . $this->getQuery();
    }

    final public function getCountQuery()
    {
        return 'SELECT COUNT(id) ' . $this->getQuery();
    }
}