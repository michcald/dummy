<?php

namespace Michcald\Dummy\App\Form\Model;

class App extends \Michcald\Form
{
    public function __construct()
    {
        $val1 = new \Michcald\Validator\String();
        $val1->setRegex('^[_a-z0-9]+$');

        $val3 = new \Michcald\Validator\String();
        $val3->setMin(6);

        $name = new \Michcald\Form\Element\Text();
        $name->setName('name')
            ->addValidator(new \Michcald\Validator\NotEmpty())
            ->addValidator($val1)
            ->addValidator($val3);
        $this->addElement($name);

        $description = new \Michcald\Form\Element\Text();
        $description->setName('description');
        $this->addElement($description);
    }
}