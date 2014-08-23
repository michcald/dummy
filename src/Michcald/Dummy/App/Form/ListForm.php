<?php

namespace Michcald\Dummy\App\Form;

class ListForm extends \Michcald\Form
{
    private $extraErrors = array();

    private $filters = array();

    private $orders = array();

    public function __construct()
    {
        $val1 = new \Michcald\Validator\String();
        $val1->setRegex('\d+');

        $page = new \Michcald\Form\Element\Number();
        $page->setName('page')
            ->addValidator($val1);
        $this->addElement($page);

        $limit = new \Michcald\Form\Element\Number();
        $limit->setName('limit')
            ->addValidator($val1);
        $this->addElement($limit);

        $query = new \Michcald\Form\Element\Text();
        $query->setName('query');
        $this->addElement($query);

        $filters = new \Michcald\Form\Element\Text();
        $filters->setName('filters');
        $this->addElement($filters);

        $orders = new \Michcald\Form\Element\Text();
        $orders->setName('orders');
        $this->addElement($orders);
    }

    public function setFilters(array $filters)
    {
        $this->filters = $filters;

        return $this;
    }

    public function setOrders(array $orders)
    {
        $this->orders = $orders;

        return $this;
    }

    public function isValid()
    {
        $valid = parent::isValid();

        $values = $this->getValues();

        if (array_key_exists('filters', $values)) {

            if (!is_array($values['filters'])) {
                $this->extraErrors[] = 'filters must be an array';
                return false;
            }

            foreach ($values['filters'] as $filter) {
                if (!array_key_exists('field', $filter) ||
                    !array_key_exists('value', $filter)) {
                    $this->extraErrors[] = sprintf('Filter "%s" must contain field and value', $filter['field']);
                    return false;
                }

                if (!in_array($filter['field'], $this->filters)) {
                    $this->extraErrors[] = sprintf('Filter "%s" not valid', $filter['field']);
                    return false;
                }
            }
        }

        if (array_key_exists('orders', $values)) {

            if (!is_array($values['orders'])) {
                $this->extraErrors[] = 'orders must be an array';
                return false;
            }

            foreach ($values['orders'] as $filter) {
                if (!array_key_exists('field', $filter) ||
                    !array_key_exists('direction', $filter)) {
                    $this->extraErrors[] = 'orders must contain field and direction';
                    return false;
                }

                if (!in_array($filter['field'], $this->orders)) {
                    $this->extraErrors[] = sprintf('Order "%s" not valid', $filter['fields']);
                    return false;
                }

                if (!in_array(strtolower($filter['direction']), array('asc', 'desc'))) {
                    $this->extraErrors[] = sprintf('Order "%s" not valid direction %s', $filter['fields'], $filter['direction']);
                    return false;
                }
            }
        }

        return $valid;
    }

    public function getErrorMessages()
    {
        return array_merge_recursive(
            parent::getErrorMessages(),
            $this->extraErrors
        );
    }
}