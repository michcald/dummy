<?php

include 'tests/TestController.php';

class AppTest extends TestController
{
    public function getUri()
    {
        return 'app';
    }

    public function getFields()
    {
        return array(
            'name',
            'description',
            'password'
        );
    }

    public function getBadItems()
    {
        return array(
            array(
                'name' => 'Test App', // only a-z0-9_
                'description' => 'Description Test',
                'password' => 'test_password'
            ),
            array(
                'name' => 'test_app',
                'description' => 'Description Test',
                'password' => 'short' // short password < 6
            ),
        );
    }

    public function getGoodItems()
    {
        return array(
            array(
                'name' => 'test_app',
                'description' => 'Description Test',
                'password' => 'test_password'
            ),
            array(
                'name' => 'test_app_1',
                'description' => 'Description Test 2',
                'password' => 'n1209nr02nr4ro3'
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

    public function testCrud()
    {
        // create
        $response = $this->rest('post', 'app', array(
            'name' => 'test_app_' . rand(0, 1000),
            'description' => 'Description Test',
            'password' => 'test_password'
        ));

        $this->assertResponse($response, 201);

        $id = $response->getContent();

        // read list
        $response = $this->rest('get', sprintf('app/%d', $id));

        $this->assertResponse(
            $response,
            200
        );

        $content = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('id', $content);
        $this->assertArrayHasKey('name', $content);
        $this->assertArrayHasKey('description', $content);
//        $this->assertArrayHasKey('password', $content);

        // verify all the fields with a validator

        $content = json_decode($response->getContent(), true);

        // update the app
        $response = $this->rest('put', sprintf('app/%d', $id), array(
            'name' => 'updated_test'
        ));

        //$this->assertResponse($response, )

        // delete the app
        $response = $this->rest('delete', sprintf('app/%d', $id));

        $this->assertResponse($response, 204);
    }
}
