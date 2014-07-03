<?php

abstract class TestController extends PHPUnit_Framework_TestCase
{
    private $rest;

    private function getGoodListParams()
    {
        $filters = array();
        foreach ($this->getListFilters() as $f) {
            $filters[] = array(
                'field' => $f,
                'value' => 'hello' . rand(0, 1000)
            );
        }

        $orders = array();
        foreach ($this->getListOrders() as $o) {
            $orders[] = array(
                'field' => $o,
                'direction' => 'asc'
            );
        }

        return array(
            array(),
            array(
                'page' => rand(0, 100000),
            ),
            array(
                'page' => rand(0, 100000),
                'limit' => rand(0, 100000),
            ),
            array(
                'page' => rand(0, 100000),
                'limit' => rand(0, 100000),
                'query' => null,
            ),
            array(
                'page' => rand(0, 100000),
                'limit' => rand(0, 100000),
                'query' => null,
                'filters' => array(),
            ),
            array(
                'page' => rand(0, 100000),
                'limit' => rand(0, 100000),
                'query' => null,
                'filters' => array(),
                'orders' => array()
            ),
            array(
                'page' => rand(0, 100000),
                'limit' => rand(0, 100000),
                'query' => null,
                'filters' => $filters,
                'orders' => $orders
            ),
        );
    }

    private function getBadListParams()
    {
        return array(
            array(
                'page' => 'asdasd',
            ),
            array(
                'page' => rand(0, 100000),
                'limit' => 'asdasdd',
            ),
            array(
                'page' => rand(0, 100000),
                'limit' => rand(0, 100000),
                'query' => null,
                'filters' => array('asdasd'),
            ),
            array(
                'page' => rand(0, 100000),
                'limit' => rand(0, 100000),
                'query' => null,
                'filters' => array(
                    array(
                        'eld' => 'asdkjbasd',
                    )
                ),
            ),
            array(
                'page' => rand(0, 100000),
                'limit' => rand(0, 100000),
                'query' => null,
                'filters' => array(
                    array(
                        'field' => 'asdkjbasd',
                    )
                ),
            ),
            array(
                'page' => rand(0, 100000),
                'limit' => rand(0, 100000),
                'query' => null,
                'filters' => array(
                    array(
                        'field' => 'asdkjbasd',
                        'value' => 'asdasd'
                    )
                ),
            ),
            array(
                'page' => rand(0, 100000),
                'limit' => rand(0, 100000),
                'query' => 'hello',
                'filters' => array(),
                'orders' => array(
                    array(
                        'field' => 'name',
                        'direction' => 'asca'
                    )
                )
            ),
        );
    }

    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $file = realpath(__DIR__ . '/../app/config/test.yml');

        $config = \Michcald\Dummy\Config::getInstance();
        if (!isset($config->test)) {
            $config->loadFile($file);
        }

        $this->rest = new \Michcald\RestClient\Client();

        $auth = new \Michcald\RestClient\Auth\Basic();
        $auth->setUsername($config->test['dummy']['auth']['username'])
            ->setPassword($config->test['dummy']['auth']['password']);

        $this->rest->setAuth($auth);
    }

    abstract public function getUri();
    abstract public function getFields();
    abstract public function getGoodItems();
    abstract public function getBadItems();
    abstract public function getListFilters();
    abstract public function getListOrders();

    protected function rest($method, $uri, array $params = array())
    {
        $config = \Michcald\Dummy\Config::getInstance();

        $url = $config->test['dummy']['endpoint'] . $uri . '/';

        switch ($method) {
            case 'get':
                return $this->rest->get($url, $params);
            case 'post':
                return $this->rest->post($url, $params);
            case 'put':
                return $this->rest->put($url, $params);
            case 'delete':
                return $this->rest->delete($url, $params);
            default:
                throw new \Exception('Invalid method');
        }
    }

    protected function assertResponse(
        Michcald\RestClient\Response $response,
        $expectedStatusCode,
        $expectedContentType = 'application/json'
    ) {
        $this->assertEquals(
            $expectedStatusCode,
            $response->getStatusCode()
        );

        $this->assertEquals(
            $expectedContentType,
            $response->getContentType()
        );
    }

    protected function validateItem(array $item)
    {
        $this->assertArrayHasKey('id', $item);

        foreach ($this->getFields() as $field) {
            $this->assertArrayHasKey($field, $item);
        }
    }

    private function validateList(\Michcald\RestClient\Response $response)
    {
        $content = json_decode($response->getContent(), true);

        $this->assertInternalType('array', $content);

        $this->assertArrayHasKey('paginator', $content);
        $this->assertInternalType('array', $content['paginator']);
        $this->assertArrayHasKey('page', $content['paginator']);
        $this->assertInternalType('array', $content['paginator']['page']);
        $this->assertArrayHasKey('current', $content['paginator']['page']);
        $this->assertArrayHasKey('total', $content['paginator']['page']);
        $this->assertArrayHasKey('results', $content['paginator']);

        $this->assertArrayHasKey('results', $content);
        $this->assertInternalType('array', $content['results']);

        foreach ($content['results'] as $item) {
            $this->validateItem($item);
        }
    }

    public function testBadCreate()
    {
        foreach ($this->getBadItems() as $item) {

            $response = $this->rest('post', $this->getUri(), $item);

            $this->assertResponse($response, 400);

            $content = json_decode($response->getContent(), true);

            $this->assertInternalType('array', $content);
            $this->assertArrayHasKey('error', $content);

            $this->assertInternalType('array', $content['error']);

            $this->assertArrayHasKey('status_code', $content['error']);
            $this->assertInternalType('int', $content['error']['status_code']);

            $this->assertArrayHasKey('message', $content['error']);

            $this->assertArrayHasKey('form', $content['error']);
            $this->assertInternalType('array', $content['error']['form']);
        }
    }

    public function testBadRead()
    {
        $response = $this->rest(
            'get',
            sprintf('%d/%d', $this->getUri(), rand(0, 100000))
        );
        $this->assertResponse($response, 404);

        $response = $this->rest(
            'get',
            sprintf('%s/%s', $this->getUri(), rand(0, 100) . 'as')
        );
        $this->assertResponse($response, 404); // no routes
    }

    public function testBadUpdate()
    {

    }

    public function testBadDelete()
    {
        $response = $this->rest(
            'delete',
            sprintf('%d/%d', $this->getUri(), rand(0, 100000))
        );
        $this->assertResponse($response, 404);

        $response = $this->rest(
            'delete',
            sprintf('%s/%s', $this->getUri(), '123sd2')
        );
        $this->assertResponse($response, 404); // no routes
    }

    public function testList()
    {
        $response = $this->rest(
            'get',
            sprintf('%s', $this->getUri())
        );
        $this->assertResponse($response, 200);

        $this->validateList($response);

        //

        foreach ($this->getGoodListParams() as $params) {
            $response = $this->rest(
                'get',
                sprintf('%s', $this->getUri()),
                $params
            );

            $this->assertResponse($response, 200);

            $this->validateList($response);
        }
    }

    public function testBadList()
    {
        foreach ($this->getBadListParams() as $params) {
            $response = $this->rest(
                'get',
                sprintf('%s', $this->getUri()),
                $params
            );

            $this->assertResponse($response, 400);

            // @TODO validate error structure
        }
    }

    abstract public function testCrud();
}