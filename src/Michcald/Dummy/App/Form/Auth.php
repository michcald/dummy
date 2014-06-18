<?php

namespace Michcald\Dummy\App\Form;

class Auth extends \Michcald\Form
{
    public function __construct()
    {
        $val = new \Michcald\Validator\String();
        $val->setMin(5)
            ->setMax(20)
            ->setRegex('[a-z0-9]+');

        $user = new \Michcald\Form\Element\Text();
        $user->setName('user')
            ->addValidator($val);
        $this->addElement($user);

        $password = new \Michcald\Form\Element\Password();
        $password->setName('password')
            ->addValidator(new \Michcald\Validator\NotEmpty());
        $this->addElement($password);
    }
}