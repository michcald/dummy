<?php

namespace Michcald\Dummy\App\Form\Model\Repository;

class Field extends \Michcald\Form
{
    private $types;

    public function __construct()
    {
        $this->types = \Michcald\Dummy\Config::getInstance()->repository['field']['types'];

        $repoId = new \Michcald\Form\Element\Text();
        $repoId->setName('repository_id')
            ->addValidator(new \Michcald\Validator\NotEmpty());
        $this->addElement($repoId);

        $type = new \Michcald\Form\Element\Select();
        $type->setName('type')
            ->addValidator(new \Michcald\Validator\NotEmpty());
        foreach ($this->types as $t) {
            $type->addOption($t['name'], $t['name']);
        }
        $this->addElement($type);

        $options = new \Michcald\Form\Element\Text();
        $options->setName('options');
        $this->addElement($options);

        $val = new \Michcald\Validator\String();
        $val->setRegex('^[a-z][_a-z0-9]*$');

        $name = new \Michcald\Form\Element\Text();
        $name->setName('name')
            ->addValidator(new \Michcald\Validator\NotEmpty())
            ->addValidator($val);
        $this->addElement($name);

        $label = new \Michcald\Form\Element\Text();
        $label->setName('label')
            ->addValidator(new \Michcald\Validator\NotEmpty());
        $this->addElement($label);

        $description = new \Michcald\Form\Element\Text();
        $description->setName('description');
        $this->addElement($description);

        $val = new \Michcald\Validator\String();
        $val->setRegex('^[01]$');

        $required = new \Michcald\Form\Element\Text();
        $required->setName('required')
            ->addValidator($val);
        $this->addElement($required);

        $searchable = new \Michcald\Form\Element\Text();
        $searchable->setName('searchable')
            ->addValidator($val);
        $this->addElement($searchable);

        $sortable = new \Michcald\Form\Element\Text();
        $sortable->setName('sortable')
            ->addValidator($val);
        $this->addElement($sortable);

        $main = new \Michcald\Form\Element\Text();
        $main->setName('main')
            ->addValidator($val);
        $this->addElement($main);

        $list = new \Michcald\Form\Element\Text();
        $list->setName('list')
            ->addValidator($val);
        $this->addElement($list);

        $val = new \Michcald\Validator\String();
        $val->setRegex('^[1-9][0-9]*$');

        $displayOrder = new \Michcald\Form\Element\Text();
        $displayOrder->setName('display_order')
            ->addValidator($val);
        $this->addElement($displayOrder);
    }

    public function isValid()
    {
        $values = $this->getValues();

        if (isset($values['type']) && $values['type'] == 'foreign') {
            $this->getElement('options')
                ->addValidator(new \Michcald\Validator\NotEmpty());
            // @TODO validation with existent repos
        }

        return parent::isValid();
    }
}