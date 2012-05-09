<?php

namespace Zubi\FaqBundle\Tests\Controller;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/* 
 * Klasa testująca działanie FaqBundle 
 * 
 * UWAGI: musi istnieć FAQ o id = 1 i nie istnieć FAQ o id = 0
 * 
 * METODY:
 * 
 * testIndex() - sprawdzenie załadowania strony z FAQ (nawet jeśli nie ma czego 
 *      wyświetlać.
 *      1. Czy status odpowiedzi http jest 200
 *      2. Czy na wyświetlaniej stronie jest wyrażenie "FAQ"
 *  
 * testFAQlist() - sprawdzenie załadowania strony z FAQ, wymaganie aby chociaż
 *      jedno FAQ było do wyświetlenia.
 *      1. Czy na stronie jest słowo "Pytanie"
 * 
 * testFaqDelete() - sprawdzenie przekierowania przy próbie usunięcia FAQ
 *      przez niezalogowanego usera.
 *      1. Czy mamy przekierowanie - status odopwiedzi 302
 * 
 * testFaqEdit() - sprawdzenie przekierowania przy próbie edycji FAQ 
 *      przez niezalogowanego usera.
 *      UWAGA: musi być FAQ o id = 1 w bazie!
 *      1. Czy mamy przekierowanie - status odopwiedzi 302
 *  
 * testFaqEditByAdmin() - sprawdzenie możliwości edycji FAQ przy 
 *     poprawnie zalogowanym użytkowniku.
 *     UWAGA: musi być FAQ o id = 1 w bazie!
 *     1. Czy status odpowiedzi http jest 200
 *     2. Czy na stroenie jest słowo: 'zmian'
 * 
 * testFaqEditByAdminNothingToEdit() - sprawdzenie przekierowania przy 
 *     próbie edycji nieistniejącego FAQ
 *     1. Status odpowiedzi http nie powinen być 200
 *     2. Czy jest przekierowanie - odpowiedź http 302 
 * 
 * testFaqEditByAdminWrongPass() - próba edycji FAQ przy złym hasle usera.
 *     1. Status odpowiedzi http nie powinen być 200
 *     2. Czy jest przekierowanie - odpowiedź http 302 
 * 
 */

class DefaultControllerTest extends WebTestCase
{
    /* 
     * testIndex() - sprawdzenie załadowania strony z FAQ (nawet jeśli nie ma czego 
     *      wyświetlać.
     *      1. Czy status odpowiedzi http jest 200
     *      2. Czy na wyświetlaniej stronie jest wyrażenie "FAQ"
     */  
    public function testIndex()
    {
        $client = static::createClient();
        // żądanie strony FAQ
        $crawler = $client->request('GET', '/faq/');                    
        $this->assertTrue(true);
        // Czy status odpowiedzi http jest 200
        $this->assertTrue($client->getResponse()->getStatusCode() == '200' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "not 200!!!!");           
        //jeśli na stronie wystąpią minimum 1 wyrażenia FAQ, strona załadowana 
        //jest poprawinie
        $this->assertTrue($crawler->filter('html:contains("FAQ")')->count() > 0);       
    }
    
    /*  
     * testFAQlist() - sprawdzenie załadowania strony z FAQ, wymaganie aby chociaż
     *      jedno FAQ było do wyświetlenia.
     *      1. Czy na stronie jest słowo "Pytanie"
     */ 
    public function testFAQlist()
    {
        $client = static::createClient();
        // żądanie strony FAQ
        $crawler = $client->request('GET', '/faq/');    
        // na liście pytania zaczynają się od słowa Pytanie.
        $this->assertRegExp('/Pytanie:/', $client->getResponse()->getContent());      
 
    }

    /* 
     * testFaqDelete() - sprawdzenie przekierowania przy próbie usunięcia FAQ
     *      przez niezalogowanego usera.
     *      1. Czy mamy przekierowanie - status odopwiedzi 302
     */ 
    public function testFaqDelete()
    {
        $client = static::createClient();
        // żądanie strony usunięcia FAQ o id = 2
        $crawler = $client->request('GET', '/faq/delete/2');    
        
        $this->assertTrue($client->getResponse()->getStatusCode() == '302' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "not 302!!!!");          
    }    

   /* 
    * testFaqEdit() - sprawdzenie przekierowania przy próbie edycji FAQ 
    *      przez niezalogowanego usera.
    *      UWAGA: musi być FAQ o id = 1 w bazie!
    *      1. Czy mamy przekierowanie - status odopwiedzi 302
    */  
    public function testFaqEdit()
    {
        $client = static::createClient();
        // żądanie strony edycji FAQ p id = 1
        $crawler = $client->request('GET', '/faq/edit/1');    
        $this->assertTrue($client->getResponse()->getStatusCode() == '302' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "not 302!!!!");   
    }

    /*  
     * testFaqEditByAdmin() - sprawdzenie możliwości edycji FAQ przy 
     *     poprawnie zalogowanym użytkowniku.
     *     UWAGA: musi być FAQ o id = 1 w bazie!
     *     1. Czy status odpowiedzi http jest 200
     *     2. Czy na stroenie jest słowo: 'zmian'
     */     
    public function testFaqEditByAdmin()
    {
        // Logowanie poprawe admina.
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'b@b.pl',
            'PHP_AUTH_PW'   => '1234'
            ));
        // Test zakłada, że istnieje w bazie FAQ o id = 1;
        // żądanie strony edycji FAQ p id = 1
        $crawler = $client->request('GET', '/faq/edit/1');    
        // Czy status odpowiedzi http jest 200
        $this->assertTrue($client->getResponse()->getStatusCode() == '200' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "not 200!!!!");   
        
        //tekst możesz dokonać zmian jest na stronie ??
        $this->assertTrue($crawler->filter('html:contains("zmian")')->count() > 0); 
    }

    /*
     *  testFaqEditByAdminNothingToEdit() - sprawdzenie przekierowania przy 
     *     próbie edycji nieistniejącego FAQ
     *     1. Status odpowiedzi http nie powinen być 200
     *     2. Czy jest przekierowanie - odpowiedź http 302 
     */ 

    public function testFaqEditByAdminNothingToEdit()
    {
         // Logowanie poprawe admina.
          $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'b@b.pl',
            'PHP_AUTH_PW'   => '1234'
            ));
        // Test zakłada, że nie istnieje w bazie FAQ o id = 0;
        // żądanie strony edycji FAQ p id = 0
        $crawler = $client->request('GET', '/faq/edit/0');   
        //Status odpowiedzi http nie powinen być 200
        $this->assertFalse($client->getResponse()->getStatusCode() == '200' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                " wrong!!!!");   
        // Czy jest przekierowanie - odpowiedź http 302
        $this->assertTrue($client->getResponse()->getStatusCode() == '302' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                " not 302!!!!");   
    }
    /*
     *  testFaqEditByAdminWrongPass() - próba edycji FAQ przy złym hasle usera.
     *     1. Status odpowiedzi http nie powinen być 200
     *     2. Czy jest przekierowanie - odpowiedź http 302 
     */   
    public function testFaqEditByAdminWrongPass()
    {
         // Złe logowanie admina - złe hasło.
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'b@b.pl',
            'PHP_AUTH_PW'   => '123'
            ));
        // Test zakłada, że istnieje w bazie FAQ o id = 1;
        // żądanie strony edycji FAQ p id = 1
        $crawler = $client->request('GET', '/faq/edit/1'); 
        //Status odpowiedzi http nie powinen być 200
        $this->assertFalse($client->getResponse()->getStatusCode() == '200' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "wrong!!!!");   
        // Czy jest przekierowanie - odpowiedź http 302
        $this->assertTrue($client->getResponse()->getStatusCode() == '302' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "not 302!!!!");   
    }

}
