<?php

namespace Zubi\ArticleBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GroupsControllerTest  extends WebTestCase {
       
    public function testIndex()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/article/groups/');        
        $this->assertTrue(true);        
        // tylko admin moze, wiec przekierowanie na strone logowania
        $this->assertTrue($client->getResponse()->getStatusCode() == '302' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "not 302!!!!");
    }
    
    public function testIndexGoodUser()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'b@b.pl',
            'PHP_AUTH_PW'   => '1234'
            ));

        $crawler = $client->request('GET', '/article/groups/');        
        $this->assertTrue(true);        
        $this->assertTrue($client->getResponse()->getStatusCode() == '200' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "not 200!!!!");
        //jeśli na stronie wystapi wyrażenie LISTA, jest poprawinie     
        $this->assertRegExp('/Lista /', $client->getResponse()->getContent());          
        //$this->assertTrue($crawler->filter('contains("Lista artykułów")')->count() > 0);
    }
    
    public function testIndexWrongUser()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'b@b.pl',
            'PHP_AUTH_PW'   => '123'
            ));

        $crawler = $client->request('GET', '/article/groups/');        
        $this->assertTrue(true);        
        $this->assertTrue($client->getResponse()->getStatusCode() == '302' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "not 302!!!!");        
    }
    
    public function testIndexGoodUserAddingGroup()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'b@b.pl',
            'PHP_AUTH_PW'   => '1234'
            ));
        $crawler = $client->request('GET', '/article/groups/');        
        $this->assertTrue(true);        
        $this->assertTrue($client->getResponse()->getStatusCode() == '200' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "not 200!!!!");        
        $form = $crawler->selectButton('submit')->form();
        $form['articleGroupform[name]'] = 'testowa grupa';
        $crawler = $client->submit($form); 
                
        $this->assertTrue($client->getResponse()->getStatusCode() == '200' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "not 200!!!!");
        $cont = $client->getResponse()->getContent();                        
        $crawler = $client->request('GET', $cont);
        $this->assertTrue($crawler->filter('html:contains("Poprawnie")')->count() > 0);
    }
    
    
    public function testIndexGoodUserEditingGroup()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'b@b.pl',
            'PHP_AUTH_PW'   => '1234'
            ));
        $crawler = $client->request('GET', '/article/groups/edit/1');        
        $this->assertTrue(true);        
        $this->assertTrue($client->getResponse()->getStatusCode() == '200' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "not 200!!!!");                               
        $form = $crawler->selectButton('submit')->form();
        $form['articleGroupform[name]'] = 'testowa grupa zmieniona';
        $crawler = $client->submit($form); 
        //po submicie przekierowanie
        $this->assertTrue($client->getResponse()->getStatusCode() == '302' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "not 302!!!!");
         $cont = $client->getResponse()->getContent();                        
         $crawler = $client->request('GET', $cont);
         $this->assertTrue($crawler->filter('html:contains("Poprawnie")')->count() > 0);   
    }
    
    
    public function testIndexGoodUserDeletingGroup()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'b@b.pl',
            'PHP_AUTH_PW'   => '1234'
            ));
        $crawler = $client->request('GET', '/article/groups/delete/1');        
        $this->assertTrue(true);        
        $this->assertFalse($client->getResponse()->getStatusCode() == '200' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "not 200!!!!");                                       
        $this->assertTrue($client->getResponse()->getStatusCode() == '302' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "not 302!!!!");
         $cont = $client->getResponse()->getContent();                        
         $crawler = $client->request('GET', $cont);
         $this->assertTrue($crawler->filter('html:contains("Skasowałeś")')->count() > 0);   
    }
}
?>