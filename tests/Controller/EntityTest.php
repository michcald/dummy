<?php

class EntityTest extends PHPUnit_Framework_TestCase
{
    const BASE_URL = 'http://localhost/dummy2/dummy2/';

    private $client;

    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $auth = new Michcald\RestClient\Auth\Basic();
        $auth->setUsername('michael')
            ->setPassword('michael');

        $this->client = new \Michcald\RestClient\Client();
        //$this->client->setAuth($auth);
    }

    private function call($method, $uri, array $params = array())
    {
        $url = self::BASE_URL . $uri . '/';

        switch ($method) {
            case 'get':
                return $this->client->get($url, $params);
            case 'post':
                return $this->client->post($url, $params);
            case 'put':
                return $this->client->put($url, $params);
            case 'delete':
                return $this->client->delete($url, $params);
            default:
                throw new \Exception('Invalid method');
        }
    }

    private function assertResponse(Michcald\RestClient\Response $response,
        $expectedStatusCode, $expectedContentType = 'application/json')
    {
        $this->assertEquals(
            $expectedStatusCode,
            $response->getStatusCode()
        );

        $this->assertEquals(
            'application/json',
            $response->getContentType()
        );
    }

    private function assertErrorResponse(Michcald\RestClient\Response $response,
        $expectedStatusCode, $expectedContentType = 'application/json')
    {
        $this->assertResponse($response, $expectedStatusCode, $expectedContentType);

        $content = json_decode($response->getContent(), true);

        $this->assertInternalType('array', $content);
        $this->assertArrayHasKey('error', $content);
        if (isset($content['error'])) {
            $this->assertArrayHasKey('status_code', $content['error']);
            $this->assertArrayHasKey('message', $content['error']);
            $this->assertContains(
                array(500),
                $content['error']['status_code']
            );
        }
    }

    public function testRead()
    {
        $response = $this->call('get', 'post_category/1');
        $this->assertResponse($response, 200);

        $response = $this->call('get', 'post_category/372634');
        $this->assertResponse($response, 404);
    }

    public function testList()
    {
        $response = $this->call('get', 'post_category');
        $this->assertResponse($response, 200);

        $content = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('paginator', $content);
        if (isset($content['paginator'])) {
            $this->assertArrayHasKey('page', $content['paginator']);
            $page = $content['paginator']['page'];
            if (isset($page)) {
                $this->assertArrayHasKey('current', $page);
                $this->assertNotNull($page['current']);
                $this->assertInternalType('int', $page['current']);
                $this->assertGreaterThanOrEqual(1, $page['current']);

                $this->assertArrayHasKey('total', $page);
                $this->assertNotNull($page['total']);
                $this->assertInternalType('int', $page['total']);
                $this->assertGreaterThanOrEqual(1, $page['total']);

                $this->assertLessThanOrEqual(
                    $page['total'],
                    $page['current']
                );
            }

            $this->assertArrayHasKey('results', $content['paginator']);
            $results = $content['paginator']['results'];
            if (isset($results)) {
                $this->assertNotNull($results);
                $this->assertInternalType('int', $results);
                $this->assertGreaterThanOrEqual(0, $results);
            }
        }

        $this->assertArrayHasKey('results', $content);

        $this->assertInternalType('array', $content['results']);

        foreach ($content['results'] as $result) {
            $this->assertInternalType('array', $result);
            $this->assertArrayHasKey('id', $result);

            if (isset($result['id'])) {
                $this->assertInternalType('int', $result['id']);
                $this->assertGreaterThan(0, $result['id']);
            }
        }
    }

    public function testCreate()
    {
        $response = $this->call('post', 'post_category', array(
            'nameasd' => 'Test'
        ));

        // error
        $this->assertErrorResponse($response, 500);

        $response = $this->call('post', 'post_category', array(
            'name' => 'Test'
        ));

        // all good
        $this->assertResponse($response, 201);

        $newEntityId = $response->getContent();

        $response = $this->call('get', 'post_category/' . $newEntityId);

        $this->assertResponse($response, 200);
    }

}
