<?php

namespace MyHammer\JobsBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CityControllerTest extends WebTestCase
{
    public function testGetcity()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/city');

        $this->assertEquals(200,$client->getResponse()->getStatusCode());

        $crawler = $client->request('GET', '/city/10115');

        $this->assertEquals(200,$client->getResponse()->getStatusCode());

        $crawler = $client->request('GET', '/city/10115644');

        $this->assertEquals(406,$client->getResponse()->getStatusCode());

        $crawler = $client->request('GET', '/city/dddd4');

        $this->assertEquals(406,$client->getResponse()->getStatusCode());
    }

}
