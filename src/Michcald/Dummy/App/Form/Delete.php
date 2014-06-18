<?php

namespace Michcald\Dummy\App\Form;

class Delete extends \Michcald\Form
{
    public function __construct()
    {
        $val = new \Michcald\Validator\String();
        $val->setRegex('\d+');

        $user = new \Michcald\Form\Element\Text();
        $user->setName('id')
            ->addValidator(new \Michcald\Validator\NotEmpty())
            ->addValidator($val);
        $this->addElement($user);
    }
}