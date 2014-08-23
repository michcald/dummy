<?php

namespace Michcald\Dummy\App\Form\Model;

class Grant extends \Michcald\Form
{
    public function __construct()
    {
        $appId = new \Michcald\Form\Element\Text();
        $appId->setName('app_id')
            ->addValidator(new \Michcald\Validator\NotEmpty());
        $this->addElement($appId);

        $repoId = new \Michcald\Form\Element\Text();
        $repoId->setName('repository_id')
            ->addValidator(new \Michcald\Validator\NotEmpty());
        $this->addElement($repoId);


        $val = new \Michcald\Validator\String();
        $val->setRegex('^[01]$');

        $create = new \Michcald\Form\Element\Text();
        $create->setName('create')
                ->addValidator($val);
        $this->addElement($create);

        $read = new \Michcald\Form\Element\Text();
        $read->setName('read')
                ->addValidator($val);
        $this->addElement($read);

        $update = new \Michcald\Form\Element\Text();
        $update->setName('update')
                ->addValidator($val);
        $this->addElement($update);

        $delete = new \Michcald\Form\Element\Text();
        $delete->setName('delete')
                ->addValidator($val);
        $this->addElement($delete);
    }
}