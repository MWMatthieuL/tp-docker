<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testShowIndex(): void
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $this->assertSelectorTextContains('html h1', 'Hello world from Symfony Skeleton!');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
