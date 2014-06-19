<?php

namespace Michcald\Dummy\App\Form\Model;

use Michcald\Dummy\App\Model\Repository;

class Entity extends \Michcald\Form
{
    public function __construct(Repository $repository)
    {
        $requiredValidator = new \Michcald\Validator\NotEmpty();

        $fkValidator = new \Michcald\Validator\String();
        $fkValidator->setRegex('^\d+$');

        foreach ($repository->getFields() as $field) {

            if ($field->getName() == 'id') {
                continue;
            }

            $element = new \Michcald\Form\Element\Text();

            $element->setName($field->getName());

            if ($field->isRequired()) {
                $element->addValidator($requiredValidator);
            }

            if ($this->isForeignKey($field)) {
                $element->addValidator($fkValidator);
            }

            $this->addElement($element);
        }
    }

    private function isForeignKey($field)
    {
        return preg_match('/^.*_id$/', $field->getName());
    }
}
