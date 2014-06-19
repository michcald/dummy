<?php

namespace Michcald\Dummy\Form\Model;

class Repository extends \Michcald\Form
{
    public function __construct()
    {
        $reqVal = new \Michcald\Validator\NotEmpty();

        $nameVal = new \Michcald\Validator\String();
        $nameVal->setMax(255)
            ->setRegex('^[a-z0-9_]*$');

        $name = new \Michcald\Form\Element\Text();
        $name->setName('name')
            ->addValidator($reqVal)
            ->addValidator($nameVal);
        $this->addElement($name);

        $description = new \Michcald\Form\Element\Textarea();
        $description->setName('description');
        $this->addElement($description);

        $labelVal = new \Michcald\Validator\String();
        $labelVal->setMax(255);

        $labelSingular = new \Michcald\Form\Element\Text();
        $labelSingular->setName('label_singular')
            ->addValidator($reqVal)
            ->addValidator($labelVal);
        $this->addElement($labelSingular);

        $labelPlural = new \Michcald\Form\Element\Text();
        $labelPlural->setName('label_plural')
            ->addValidator($reqVal)
            ->addValidator($labelVal);
        $this->addElement($labelPlural);
    }
}