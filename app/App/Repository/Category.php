<?php

namespace App\Repository;

class Category extends \Michcald\Dummy\Repository
{
    public function __construct()
    {
        parent::__construct();

        $name = new \Michcald\Dummy\Entity\Field\String('name');
        $name->setLabel('Category Name')
                ->setDescription('Category name')
                ->setExpose(true)
                ->setSearchable(true);
        $this->addField($name);

        $description = new \Michcald\Dummy\Entity\Field\Text('description');
        $description
                ->setLabel('Category Description')
                ->setDescription('Insert the descr')
                ->setExpose(false)
                ->setSearchable(false);
        $this->addField($description);
    }

    public function getName()
    {
        return 'post_category';
    }

    public function getLabel($plural = false)
    {
        return $plural ? 'Post Categories' : 'Post Category';
    }

    public function getParentEntities()
    {
        return array('post');
    }

    public function getMaxRecords()
    {
        return 5;
    }
}

