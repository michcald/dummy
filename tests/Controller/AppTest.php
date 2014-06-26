<?php

class AppTest extends PHPUnit_Framework_TestCase
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
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertEquals(
            'application/json',
            $response->getContentType()
        );
    }

    public function testCrud()
    {
        // read list
        $response = $this->call('get', 'app');
        $this->assertResponse($response, 200);

        $content = json_decode($response->getContent(), true);

        //$this->assertArrayHasKey('statu', $content);

    }

}
