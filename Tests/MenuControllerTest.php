<?php

namespace PBE\BaseBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class MenuControllerTest extends WebTestCase
{
    /**
     * Testing the top menu controller
     * @TODO: improving the test
     */
    public function testTopMenu()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/menu/top_menu_from_folder/2');
        $this->assertGreaterThan( 0, $crawler->filter('ul li')->count(), "No menu entries found!" );
        $this->assertEquals( 0, $crawler->filter('head')->count(), "Found a head-tag, maybe routing_test.yml is not enabled!" );
    }
}
