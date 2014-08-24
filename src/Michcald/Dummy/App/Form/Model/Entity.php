<?php

namespace Michcald\Dummy\App\Form\Model;

use Michcald\Dummy\App\Model\Repository;

class Entity extends \Michcald\Form
{
    public function __construct(array $fields)
    {
        $requiredValidator = new \Michcald\Validator\NotEmpty();

        /* @var $field Repository\Field */
        foreach ($fields as $field) {

            if ($field->getName() == 'id') {
                continue;
            }

            $element = new \Michcald\Form\Element\Text();

            $element->setName($field->getName());

            if ($field->isRequired()) {
                $element->addValidator($requiredValidator);
            }

            $this->addElement($element);
        }
    }
}
