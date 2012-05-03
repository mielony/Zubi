<?php

namespace Zubi\IndexBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zubi\IndexBundle\Tests\AbstractAdminTestCase;

class DefaultControllerTest extends AbstractAdminTestCase {
    
    public function testUnloggedIndex() {

        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testLoggedIndex() {
        $client = $this->createClientWithAuthentication('secured_area');

        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }
}
