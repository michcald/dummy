<?php

require_once 'tests/TestController.php';

class RepositoryTest extends TestController
{
    public function getUri()
    {
        return 'repository';
    }

    public function getFields()
    {
        return array(
            array(
                'name'     => 'name',
                'required' => true, // 1 < length < 255, a-z0-9_
            ),
            array(
                'name'     => 'description',
                'required' => false, // length < 255
            ),
            array(
                'name'     => 'label_singular',
                'required' => true, // length < 255
            ),
            array(
                'name'     => 'label_plural',
                'required' => true, // length < 255
            ),
        );
    }

    public function getBadItems()
    {
        return array(
            array(),
            array(
                'name' => 'n34i24,.213',
            ),
            array(
                'name' => 'hello',
                'label_singular' => null
            ),
        );
    }

    public function getGoodItems()
    {
        return array(
            array(
                'name' => 'asddasdas2349i',
                'label_singular' => 'Singular label',
                'label_plural' => 'Plural label'
            ),
            array(
                'name' => 'asddasweddas2349i',
                'description' => 'asdasdasd',
                'label_singular' => 'Singular label',
                'label_plural' => 'Plural label'
            ),
        );
    }

    public function getListFilters()
    {
        return array(
            'name'
        );
    }

    public function getListOrders()
    {
        return array(
            'name',
        );
    }
}
