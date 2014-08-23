<?php

require_once 'tests/TestController.php';

class AppTest extends TestController
{
    public function getUri()
    {
        return 'app';
    }

    public function getFields()
    {
        return array(
            array(
                'name' => 'name',
                'required' => true
            ),
            array(
                'name' => 'description',
                'required' => false
            ),
            array(
                'name' => 'password',
                'required' => true
            )
        );
    }

    public function getBadItems()
    {
        return array(
            array(
                'name' => 'Test App', // only a-z0-9_
                'description' => 'Description Test',
                'password' => 'test_password',
            ),
            array(
                'name' => 'test_app',
                'description' => 'Description Test',
                'password' => 'short' // short password < 6
            ),
            array(
                'name' => 'test_app',
                // password not provided
            ),
            array(
                // name not provided
                'password' => 'test_password',
            ),
            array(
                'name' => null,
                'password' => 'test_password',
            ),
            array(
                'name' => '12345', // min length 6
                'password' => 'test_password',
            ),
            array(),
            array(
                'asdasd',
                'asdsd'
            ),
        );
    }

    public function getGoodItems()
    {
        return array(
            array(
                'name' => 'test_app_' . rand(0, 1000000),
                'description' => 'Description Test',
                'password' => 'test_password'
            ),
            array(
                'name' => 'test_app_' . rand(0, 1000000),
                'description' => 'Description Test 2',
                'password' => 'n1209nr02nr4ro3'
            ),
            array(
                'name' => 'test_app_' . rand(0, 1000000),
                'description' => 'Description Test 2',
                'password' => '123456' // min length 6
            ),
            array(
                'name' => 'test_app_' . rand(0, 1000000),
                'description' => null,
                'password' => '123456' // min length 6
            ),
            array(
                'name' => 'test_app_' . rand(0, 1000000),
                'password' => '123456' // min length 6
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
