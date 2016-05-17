<?php
namespace App;

use Silex\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function createApplication()
    {
        $app = require __DIR__ . '/../../app/application.php';
        $app['debug'] = true;
        $app['exception_handler']->disable();
        $app['session.test'] = true;
        return $app;
    }

    public function testIsUserFetchValidJson()
    {
        $client = $this->createClient();
        $client->request('GET', '/user/2');
        $response = $client->getResponse();
        $data = $response->getContent();
        $this->assertJson($data);
    }
}