<?php

namespace MyHammer\JobsBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CityControllerTest extends WebTestCase
{
    public function testGetcity()
    {
        $client = static::createClient();

        $crawler = $client->request('POST', '/city');

        $this->assertEquals(200,$client->getResponse()->getStatusCode());


        $response = $client->request(
            'POST',
            '/city',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
                '{"zipcode" : 10115}'
        );

        $this->assertEquals(200,$client->getResponse()->getStatusCode());

        $response = $client->request(
            'POST',
            '/city',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
                '{"zipcode" : 10115644}'
        );

        $this->assertEquals(406,$client->getResponse()->getStatusCode());

        $response = $client->request(
            'POST',
            '/city',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
                '{"zipcode" : dddd4}'
        );


        $this->assertEquals(400,$client->getResponse()->getStatusCode());
    }

}
