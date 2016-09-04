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
        self::assertSame(200, $client->getResponse()->getStatusCode());
    }


}
