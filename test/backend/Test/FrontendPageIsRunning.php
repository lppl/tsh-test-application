<?php
/**
 * (c) Łukasz Pietrek.
 */

declare(strict_types = 1);


namespace TSH\Local\Test;


use Silex\Application;
use Silex\WebTestCase;

class FrontendPageIsRunning extends WebTestCase
{

    public function createApplication(): Application
    {
        $app = require __DIR__ . '/../../../app.php';
        $app['debug'] = true;
        unset($app['exception_handler']);
        return $app;
    }


    /** @test */
    public function indexPage()
    {
        $client = $this->createClient();
        $client->request('GET', '/');
        $response = $client->getResponse();
        self::assertSame(200, $response->getStatusCode());
        self::assertSame('text/html;charset=UTF-8', $response->headers->get('content-type'));
    }

    /** @test */
    public function jsonResponse()
    {
        $client = $this->createClient();
        $client->request('GET', '/json');

        $response = $client->getResponse();
        self::assertSame(200, $response->getStatusCode());
        self::assertSame('application/json', $response->headers->get('content-type'));
    }


}
