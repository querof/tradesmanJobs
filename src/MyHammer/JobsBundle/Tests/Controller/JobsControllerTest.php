<?php

namespace MyHammer\JobsBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class JobsControllerTest extends WebTestCase
{
    public function testGet()
    {
        $client = static::createClient();

        $response = $client->request(
            'POST',
            '/jobs',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
                '{"user" : 1}'
        );

        $this->assertEquals(200,$client->getResponse()->getStatusCode());

        $response = $client->request(
            'POST',
            '/jobs',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
                '{"user" : 1,
                  "zipcode" : "10115",
                  "service" : 802030}'
        );

        $this->assertEquals(200,$client->getResponse()->getStatusCode());

        $response = $client->request(
            'POST',
            '/jobs',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
                '{"user" : 1,
                  "zipcode" : "1011555",
                  "service" : 802030}'
        );

        $this->assertEquals(406,$client->getResponse()->getStatusCode());
    }

    public function testId()
    {
        $client = static::createClient();
        $jobId = 2;
        $crawler = $client->request('GET', '/jobs/'.$jobId);
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }

    public function testPost()
    {

        $client = static::createClient();

        $response = $client->request(
            'POST',
            '/jobs/',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
                '{"city" : 2,
                "service" : 411070,
                "title" : "Get ready for MyHammer modifyed",
                "description" : "Get ready for MyHammer, develop custom made APIs",
                "date" : "2018/09/01",
                "user" : 1}'
        );

         $this->assertEquals(201, $client->getResponse()->getStatusCode());

         $response = $client->request(
             'POST',
             '/jobs/',
             array(),
             array(),
             array('CONTENT_TYPE' => 'application/json'),
                 '{"city" : 2,
                 "service" : 411070,
                 "title" : "G",
                 "description" : "Get ready for MyHammer, develop custom made APIs",
                 "date" : "2018/09/01",
                 "user" : 1}'
         );

         $this->assertEquals(406, $client->getResponse()->getStatusCode());

         $response = $client->request(
             'POST',
             '/jobs/',
             array(),
             array(),
             array('CONTENT_TYPE' => 'application/json'),
                 '{"city" : 2,
                 "service" : 411070,
                 "title" : "Get ready for MyHammer modifyed Get ready for MyHammer modifyed",
                 "description" : "Get ready for MyHammer, develop custom made APIs",
                 "date" : "2018/09/01",
                 "user" : 1}'
         );

         $this->assertEquals(406, $client->getResponse()->getStatusCode());

    }

    public function testUpdate()
    {
      $client = static::createClient();

      $response = $client->request(
          'PUT',
          '/jobs/2',
          array(),
          array(),
          array('CONTENT_TYPE' => 'application/json'),
              '{"city" : 2,
              "service" : 411070,
              "title" : "Get ready for MyHammer",
              "description" : "Get ready for MyHammer, develop custom made APIs",
              "date" : "2018/09/01",
              "user" : 1}'
      );

       $this->assertEquals(200, $client->getResponse()->getStatusCode());

       $response = $client->request(
           'PUT',
           '/jobs/2',
           array(),
           array(),
           array('CONTENT_TYPE' => 'application/json'),
               '{"city" : 2,
               "service" : 411070,
               "title" : "G",
               "description" : "Get ready for MyHammer, develop custom made APIs",
               "date" : "2018/09/01",
               "user" : 1}'
       );

       $this->assertEquals(406, $client->getResponse()->getStatusCode());

       $response = $client->request(
           'PUT',
           '/jobs/2',
           array(),
           array(),
           array('CONTENT_TYPE' => 'application/json'),
               '{"city" : 2,
               "service" : 411070,
               "title" : "Get ready for MyHammer modifyed Get ready for MyHammer modifyed",
               "description" : "Get ready for MyHammer, develop custom made APIs",
               "date" : "2018/09/01",
               "user" : 1}'
       );

       $this->assertEquals(406, $client->getResponse()->getStatusCode());
    }

}
