<?php

namespace Zubi\ArticleBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    //testowanie strony z listą artykułów
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/article/');        
        $this->assertTrue(true);        
        $this->assertTrue($client->getResponse()->getStatusCode() == '200' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "not 200!!!!");
        //jeśli na stronie wystapi wyrażenie LISTA, jest poprawinie     
        $this->assertRegExp('/Lista artykułów/', $client->getResponse()->getContent());  
        $this->assertTrue($crawler->filter('h3:contains("Li")')->count() > 0);    
        //$this->assertTrue($crawler->filter('contains("Lista artykułów")')->count() > 0);
    }
    
    //testowanie strony wyśietlającej artykuł
    public function testShowArticle()
    {       
        $client = static::createClient();        
        $crawler = $client->request('GET', '/article/show/1');
        $this->assertTrue(true);          
        $this->assertTrue($client->getResponse()->getStatusCode() == '200' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "not 200!!!!");
        $cont = $client->getResponse()->getContent();                        
        $this->assertTrue($crawler->filter('a:contains("do listy")')->count() > 0);    
       // $link = $crawler->filter('a:contains("Greet")')->eq(1)->link();
    }      
    
    public function testDeleteArticleGoodUserWrongId()
    {       
         $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'b@b.pl',
            'PHP_AUTH_PW'   => '1234'
            ));
        $crawler = $client->request('GET', '/article/delete/0');
        $this->assertTrue(true);          
        $this->assertFalse($client->getResponse()->getStatusCode() == '200' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "not 200!!!!");       
        $this->assertTrue($client->getResponse()->getStatusCode() == '302' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "not 302!!!!");       
    }
    
    public function testDeleteArticleWrongUser()
    {       
         $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'b@b.pl',
            'PHP_AUTH_PW'   => '123s'
            ));
        $crawler = $client->request('GET', '/article/delete/0');
        $this->assertTrue(true);          
        $this->assertFalse($client->getResponse()->getStatusCode() == '200' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "not 200!!!!");       
        $this->assertTrue($client->getResponse()->getStatusCode() == '302' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "not 302!!!!");       
    }
    
    //musi być artykuł o id = 1 
    public function testDeleteArticleGoodUserGoodId()
    {       
         $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'b@b.pl',
            'PHP_AUTH_PW'   => '1234'
            ));
        $crawler = $client->request('GET', '/article/delete/1');
        $this->assertTrue(true);          
        $this->assertFalse($client->getResponse()->getStatusCode() == '200' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "not 200!!!!");       
        $this->assertTrue($client->getResponse()->getStatusCode() == '302' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "not 302!!!!");       
        $cont = $client->getResponse()->getContent();                        
        $crawler = $client->request('GET', $cont);
        //jeśli po przekierowaniu w html jest tekst Sukes oznacza to że został
        // wyświetlony komunikat o sukcesie kasowania
        $this->assertTrue($crawler->filter('html:contains("Sukces")')->count() > 0);                
    }
    
    
    public function testAddArticleGoodUser()
    {       
         $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'b@b.pl',
            'PHP_AUTH_PW'   => '1234'
            ));
        $crawler = $client->request('GET', '/article/add/');        
        $this->assertTrue($client->getResponse()->getStatusCode() == '200' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "not 200!!!!");       
        $this->assertTrue($crawler->filter('html:contains("nowy")')->count() > 0);                
    }
    
     public function testAddArticleWrongUser()
    {       
         $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'b@b.pl',
            'PHP_AUTH_PW'   => '123s'
            ));
        $crawler = $client->request('GET', '/article/add/');
        $this->assertTrue(true);          
        $this->assertFalse($client->getResponse()->getStatusCode() == '200' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "not 200!!!!");       
        $this->assertTrue($client->getResponse()->getStatusCode() == '302' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "not 302!!!!");       
    }
    
    
     public function testAddArticleGoodUserGoodData()
    {       
         $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'b@b.pl',
            'PHP_AUTH_PW'   => '1234'
            ));
        $crawler = $client->request('GET', '/article/add/');        
        $this->assertTrue($client->getResponse()->getStatusCode() == '200' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "not 200!!!!");       
        $this->assertTrue($crawler->filter('html:contains("nowy")')->count() > 0);                
        //var_dump($client->getResponse()->getContent());
        //$crawler = $client->request('GET', '/article/add/');        
        $form = $crawler->selectButton('submit')->form();

        $form['articleform[title]'] = 'some test title ';
        $form['articleform[content]'] = 'some test content';

        $crawler = $client->submit($form); 
        
        //po submicie przekierowanie
        $this->assertTrue($client->getResponse()->getStatusCode() == '302' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "not 302!!!!");
         
        //$crawler = $client->request('GET',);
       //  var_dump($client->getResponse()->getContent());
         $cont = $client->getResponse()->getContent();                        
         $crawler = $client->request('GET', $cont);
         $this->assertTrue($crawler->filter('html:contains("Poprawnie")')->count() > 0);
      //   var_dump($client->getResponse()->getContent());
    }
            
}
