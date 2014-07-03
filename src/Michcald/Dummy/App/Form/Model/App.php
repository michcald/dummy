<?php

namespace Michcald\Dummy\App\Form\Model;

class App extends \Michcald\Form
{
    public function __construct()
    {
        $val1 = new \Michcald\Validator\String();
        $val1->setRegex('^[_a-z0-9]+$');

        $name = new \Michcald\Form\Element\Text();
        $name->setName('name')
            ->addValidator(new \Michcald\Validator\NotEmpty())
            ->addValidator($val1);
        $this->addElement($name);

        $description = new \Michcald\Form\Element\Text();
        $description->setName('description');
        $this->addElement($description);

        $val2 = new \Michcald\Validator\String();
        $val2->setMin(6);

        $password = new \Michcald\Form\Element\Password();
        $password->setName('password')
            ->addValidator(new \Michcald\Validator\NotEmpty())
            ->addValidator($val2);
        $this->addElement($password);
    }

    public function getValues()
    {
        $values = parent::getValues();

        $values['password'] = sha1($values['password']);

        return $values;
    }
}