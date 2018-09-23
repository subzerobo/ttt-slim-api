<?php

namespace Tests\Functional;

class WebInterfaceTest extends BaseTestCase
{
    /**
     * Test that the index route returns a rendered response containing the text 'Tic-Tac-Toe Game'
     *
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     */
    public function testGetWebInterface()
    {
        $response = $this->runApp('GET', '/');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Tic-Tac-Toe Game', (string)$response->getBody());
    }

    /**
     * Test that the index route won't accept a post request
     *
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     */
    public function testPostWebInterfaceNotAllowed()
    {
        $response = $this->runApp('POST', '/', ['test']);

        $this->assertEquals(405, $response->getStatusCode());
        $this->assertContains('Method not allowed', (string)$response->getBody());
    }
}