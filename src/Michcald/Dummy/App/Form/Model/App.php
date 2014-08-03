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

        $val = new \Michcald\Validator\String();
        $val->setRegex('^[01]$');

        $admin = new \Michcald\Form\Element\Text();
        $admin->setName('is_admin')
                ->addValidator($val);
        $this->addElement($admin);

        $publicKey = new \Michcald\Form\Element\Text();
        $publicKey->setName('public_key')
            ->addValidator(new \Michcald\Validator\NotEmpty())
            ->addValidator($val1);
        $this->addElement($publicKey);

        $privateKey = new \Michcald\Form\Element\Text();
        $privateKey->setName('private_key')
            ->addValidator(new \Michcald\Validator\NotEmpty())
            ->addValidator($val1);
        $this->addElement($privateKey);
    }
}