<?php

namespace Michcald\Dummy\App\Form\Model;

use Michcald\Dummy\App\Model\Repository;

class Entity extends \Michcald\Form
{
    private $fields;

    public function __construct(array $fields)
    {
        $this->fields = $fields;

        $requiredValidator = new \Michcald\Validator\NotEmpty();

        /* @var $field Repository\Field */
        foreach ($fields as $field) {

            if ($field->getName() == 'id') {
                continue;
            }

            $element = new \Michcald\Form\Element\Text();

            $element->setName($field->getName());

            if ($field->isRequired()) {
                if ($field->getType() != 'file') {
                    $element->addValidator($requiredValidator);
                }
            }

            $this->addElement($element);
        }
    }

    public function isValid()
    {
        $res = parent::isValid();

        $values = $this->getValues();

        foreach ($this->fields as $field) {

            if ($field->getType() == 'file' && $field->isRequired()) {
                if (!is_array($values[$field->getName()])) {
                    return false;
                }
            }
        }

        return $res;
    }
}
