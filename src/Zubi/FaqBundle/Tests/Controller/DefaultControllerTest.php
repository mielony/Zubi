<?php

namespace Zubi\FaqBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/faq/');            
        
        $this->assertTrue(true);
        //jeśli na stronie wystąpią minimum 1 wyrażenia FAQ, strona załadowana 
        //jest poprawinie
        $this->assertTrue($crawler->filter('html:contains("FAQ")')->count() > 0);       
    }
    
    public function testFAQlist()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/faq/');    
        $this->assertRegExp('/1./', $client->getResponse()->getContent());      
 
    }
    
    public function testFaqDelete()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/faq/delete/2');    
        $this->assertTrue($client->getResponse()->getStatusCode() == '302' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "not 302!!!!");          
    }    
    
    public function testFaqEdit()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/faq/edit/2');    
        $this->assertTrue($client->getResponse()->getStatusCode() == '302' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "not 302!!!!");   
    }

    public function testFaqEditByAdmin()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'b@b.pl',
            'PHP_AUTH_PW'   => '1234'
            ));
        //Test zakłada, że istnieje w bazie FAQ o id = 1;
        $crawler = $client->request('GET', '/faq/edit/1');    
        $this->assertTrue($client->getResponse()->getStatusCode() == '200' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "not 200!!!!");   
        //sprawdzam czy jest słowo EDYCJA na stronie - projekt zakłada że jest.
        $this->assertTrue($crawler->filter('html:contains("EDYCJA")')->count() > 0); 
    }
    
    public function testFaqEditByAdminNothingToEdit()
    {
          $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'b@b.pl',
            'PHP_AUTH_PW'   => '123'
            ));
        //Test zakłada, że nie istnieje w bazie FAQ o id = 0;
        $crawler = $client->request('GET', '/faq/edit/0');    
        $this->assertFalse($client->getResponse()->getStatusCode() == '200' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "not 302!!!!");   
        $this->assertTrue($client->getResponse()->getStatusCode() == '302' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "not 302!!!!");   
    }
    
    public function testFaqEditByAdminWrongPass()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'b@b.pl',
            'PHP_AUTH_PW'   => '123'
            ));
        //Test zakłada, że istnieje w bazie FAQ o id = 1;
        $crawler = $client->request('GET', '/faq/edit/1');    
        $this->assertFalse($client->getResponse()->getStatusCode() == '200' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "not 302!!!!");   
        $this->assertTrue($client->getResponse()->getStatusCode() == '302' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "not 302!!!!");   
    }
    
    
    
    
}
