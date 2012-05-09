<?php
namespace Zubi\ArticleBundle\Tests\Controller;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
/* 
 * Klasa testująca działanie ArticleBundle 
 * 
 * UWAGI: Aby poprawnie przeprowadzić testy należy stworzyć w bazie danych
 *        usera b@b.pl z hasłem 1234, który jest administratorem. Ew. odpowiednio
 *        zmienić kod metod.
 * 
 * METODY:
 * 
 *  testIndex() - sprawdza czy poprawnie wyświetliła się strona z listą akrtykułów.
 *        1. Czy status odpowiedzi http jest 200
 *        2. Czy na wyświetlaniej stronie jest nagłówek "Lista artykułów"
 *        3. Czy w źrodel strony występuje znacznik LI oznaczający listę, 
 *           to za jego pomocą prezentowana jest lista.
 * 
 *  testShowArticle() - sprawdza poprawność prezentacji strony z jednym artykułem
 *       UWAGA - w bazie musi istnieć atykuł o id = 1
 *       1. Czy status odpowiedzi http jest 200
 *       2. Czy na wyświetlaniej stronie jest link do powrotu.
 * 
 *  testDeleteArticleGoodUserWrongId() - sprawdza czy system przekieruje strone 
 *       w razie próby kasowania artykułu o nieistniejacym id.
 *       1. Czy status odpowiedzi http jest różny od 200
 *       2. Czy status odpowiedzi http jest równy 302
 *  
 *  testDeleteArticleWrongUser() - sprawdza przekierowanie strony w razie 
 *       próby usunięcia artykułu przy źle zalogowanym użytkowniku (dobry user, 
 *       złe hasło)
 *       UWAGA - w bazie musi istnieć atykuł o id = 1
 *       1. Czy status odpowiedzi http jest różny od 200
 *       2. Czy status odpowiedzi http jest równy 302
 * 
 *  testDeleteArticleGoodUserGoodId() - sprawdza możliwość skasowania artykułu 
 *       oraz czy został poprawnie usunięty (komunikat o sukcesie)
 *       UWAGA - w bazie musi istnieć atykuł o id = 1
 *       1. Czy status odpowiedzi http jest różny od 200
 *       2. Czy status odpowiedzi http jest równy 302
 *       3. Czy na wyświetlaniej stronie po usunięciu artykułu 
 *          jest wyświetlany napis "Sukces"
 * 
 *  testAddArticleGoodUser() - sprawdza popraność wyświetlenia strony dodawnia
 *       artykułu. Przy poprawnym zalogowaniu 
 *       1. Czy status odpowiedzi http jest 200
 *       2. Sprawdza czy na stronie pojawia się wyrążnie Nowy
 * 
 *  testAddArticleWrongUser() - sprawdza przekierowanie strony w razie 
 *       próby dodania artykułu przy źle zalogowanym użytkowniku (dobry user, 
 *       złe hasło)
 *       1. Czy status odpowiedzi http jest różny od 200
 *       2. Czy status odpowiedzi http jest równy 302
 * 
 *  testAddArticleGoodUserGoodData() - sprawdzenie możliwości dodania nowego 
 *       artykułu. Metoda dobrze się loguje, wypełnia formularz, zapisuje dane, 
 *       sprawdza czy zostały zapisane.
 *       1. Czy status odpowiedzi http jest 200
 *       2. Sprawdza czy na stronie pojawia się wyrążnie Nowy 
 *       3. Wypełnia formularz
 *          3.1 W pole tytuł wpisuje  'some test title '
 *          3.2 W pole treść wpisuje 'some test content'
 *       4. Zatwierdza formualrz 
 *       5. Czy status odpowiedzi http jest równy 302
 *       6. Czy na stronie po przekierowaniu mamy napis "Poprawnie"
 */

class DefaultControllerTest extends WebTestCase
{
    /* 
     *  testIndex() - sprawdza czy poprawnie wyświetliła się strona z listą akrtykułów.
     *        1. Czy status odpowiedzi http jest 200
     *        2. Czy na wyświetlaniej stronie jest nagłówek "Lista artykułów"
     *        3. Czy w źrodel strony występuje znacznik LI oznaczający listę, 
     *           to za jego pomocą prezentowana jest lista.
     */
    public function testIndex()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/article/');        
        $this->assertTrue(true);        
        // Czy status odpowiedzi http jest 200
        $this->assertTrue($client->getResponse()->getStatusCode() == '200' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "not 200!!!!");           
        // Czy na wyświetlaniej stronie jest nagłówek "Lista artykułów"
        $this->assertRegExp('/Lista artykułów/', $client->getResponse()->getContent());  
        // Czy w źrodel strony występuje znacznik LI oznaczający listę, 
        // to za jego pomocą prezentowana jest lista.   
        $this->assertTrue($crawler->filter('h3:contains("Li")')->count() > 0);            
    }
    
     /*
      *   testShowArticle() - sprawdza poprawność prezentacji strony z jednym artykułem      
      *       UWAGA - w bazie musi istnieć atykuł o id = 1
      *       1. Czy status odpowiedzi http jest 200
      *       2. Czy na wyświetlaniej stronie jest link do powrotu.     
      */
    public function testShowArticle()
    {       
        $client = static::createClient();       
        //żadnanie strony artykułu o id = 1
        $crawler = $client->request('GET', '/article/show/1');
        $this->assertTrue(true);  
        //Czy status odpowiedzi http jest 200
        $this->assertTrue($client->getResponse()->getStatusCode() == '200' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "not 200!!!!");
        $cont = $client->getResponse()->getContent();  
        //Czy na wyświetlaniej stronie jest link do powrotu.
        $this->assertTrue($crawler->filter('a:contains("do listy")')->count() > 0);    
    }      
    
     /* 
      *  testDeleteArticleGoodUserWrongId() - sprawdza czy system przekieruje strone 
      *       w razie próby kasowania artykułu o nieistniejacym id.
      *       1. Czy status odpowiedzi http jest różny od 200
      *       2. Czy status odpowiedzi http jest równy 302
      * 
      */
    public function testDeleteArticleGoodUserWrongId()
    {       
        // Logowanie poprawnego usera (admina)
         $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'b@b.pl',
            'PHP_AUTH_PW'   => '1234'
            ));
        // Żądanie strony usunięcia artykułu, ktróry nie istnieje
        $crawler = $client->request('GET', '/article/delete/0');
        $this->assertTrue(true);          
        // Czy status odpowiedzi http jest różny od 200
        $this->assertFalse($client->getResponse()->getStatusCode() == '200' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "Wrong!!!!");       
        // Czy status odpowiedzi http jest równy 302
        $this->assertTrue($client->getResponse()->getStatusCode() == '302' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "not 302!!!!");       
    }
    
     /* 
      *  testDeleteArticleWrongUser() - sprawdza przekierowanie strony w razie 
      *       próby usunięcia artykułu przy źle zalogowanym użytkowniku (dobry user, 
      *       złe hasło)
      *       UWAGA - w bazie musi istnieć atykuł o id = 1
      *       1. Czy status odpowiedzi http jest różny od 200
      *       2. Czy status odpowiedzi http jest równy 302
      * 
      */
    public function testDeleteArticleWrongUser()
    {       
        // Logowanie ze złymi danynymi
         $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'b@b.pl',
            'PHP_AUTH_PW'   => '123s'
            ));
        // Żądanie strony usunięcia artykułu
        $crawler = $client->request('GET', '/article/delete/1');
        $this->assertTrue(true);          
        // Czy status odpowiedzi http jest różny od 200
        $this->assertFalse($client->getResponse()->getStatusCode() == '200' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "Wrong!!!!");       
        // Czy status odpowiedzi http jest równy 302
        $this->assertTrue($client->getResponse()->getStatusCode() == '302' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "not 302!!!!");       
    }    
    
    /* 
     *  testDeleteArticleGoodUserGoodId() - sprawdza możliwość skasowania artykułu 
     *       oraz czy został poprawnie usunięty (komunikat o sukcesie)
     *       UWAGA - w bazie musi istnieć atykuł o id = 1
     *       1. Czy status odpowiedzi http jest różny od 200
     *       2. Czy status odpowiedzi http jest równy 302
     *       3. Czy na wyświetlaniej stronie po usunięciu artykułu 
     *          jest wyświetlany napis "Sukces"
     * 
     */
    public function testDeleteArticleGoodUserGoodId()
    {       
        // Logowanie poprawnego usera (admina)
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'b@b.pl',
            'PHP_AUTH_PW'   => '1234'
            ));
        // Żądanie strony usunięcia artykułu
        $crawler = $client->request('GET', '/article/delete/1');
        $this->assertTrue(true);          
        // Czy status odpowiedzi http jest różny od 200
        $this->assertFalse($client->getResponse()->getStatusCode() == '200' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "wrong!!!!");       
        // Czy status odpowiedzi http jest równy 302
        $this->assertTrue($client->getResponse()->getStatusCode() == '302' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "not 302!!!!");       
        //pobranie strony po przekierowaniu
        $cont = $client->getResponse()->getContent();                        
        $crawler = $client->request('GET', $cont);
        //jeśli po przekierowaniu w html jest tekst Sukes oznacza to że został
        // wyświetlony komunikat o sukcesie kasowania
        $this->assertTrue($crawler->filter('html:contains("Sukces")')->count() > 0);                
    }
    
     /* 
      *  testAddArticleGoodUser() - sprawdza popraność wyświetlenia strony dodawnia
      *       artykułu. Przy poprawnym zalogowaniu 
      *       1. Czy status odpowiedzi http jest 200
      *       2. Sprawdza czy na stronie pojawia się wyrążnie Nowy
      * 
      */
    public function testAddArticleGoodUser()
    {      
        // Logowanie poprawnego usera (admina)
         $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'b@b.pl',
            'PHP_AUTH_PW'   => '1234'
            ));
        // Żądanie strony dodawania artykułu
        $crawler = $client->request('GET', '/article/add/');        
        // Czy status odpowiedzi http jest 200
        $this->assertTrue($client->getResponse()->getStatusCode() == '200' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "not 200!!!!");       
        // Sprawdza czy na stronie pojawia się wyrążnie Nowy
        $this->assertTrue($crawler->filter('html:contains("nowy")')->count() > 0);                
    }
    
    /* 
     *  testAddArticleWrongUser() - sprawdza przekierowanie strony w razie 
     *       próby dodania artykułu przy źle zalogowanym użytkowniku (dobry user, 
     *       złe hasło)
     *       1. Czy status odpowiedzi http jest różny od 200
     *       2. Czy status odpowiedzi http jest równy 302    
     * 
     */
    public function testAddArticleWrongUser()
    {       
        // Logowanie ze złymi danynymi
         $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'b@b.pl',
            'PHP_AUTH_PW'   => '123s'
            ));
        // Żądanie strony dodawania artykułu
        $crawler = $client->request('GET', '/article/add/');
        $this->assertTrue(true);     
        //Czy status odpowiedzi http jest różny od 200
        $this->assertFalse($client->getResponse()->getStatusCode() == '200' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "wrong!!!!");       
        //Czy status odpowiedzi http jest równy 302 
        $this->assertTrue($client->getResponse()->getStatusCode() == '302' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "not 302!!!!");       
    }
    
     /* 
      *  testAddArticleGoodUserGoodData() - sprawdzenie możliwości dodania nowego 
      *       artykułu. Metoda dobrze się loguje, wypełnia formularz, zapisuje dane, 
      *       sprawdza czy zostały zapisane.
      *       1. Czy status odpowiedzi http jest 200
      *       2. Sprawdza czy na stronie pojawia się wyrażnie Nowy 
      *       3. Wypełnia formularz
      *          3.1 W pole tytuł wpisuje  'some test title '
      *          3.2 W pole treść wpisuje 'some test content'
      *       4. Zatwierdza formualrz 
      *       5. Czy status odpowiedzi http jest równy 302
      *       6. Czy na stronie po przekierowaniu mamy napis "Poprawnie"
      */
     public function testAddArticleGoodUserGoodData()
    {      
         // Logowanie poprawnego usera (admina)
         $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'b@b.pl',
            'PHP_AUTH_PW'   => '1234'
            ));
        // Żądanie strony dodawania artykułu
        $crawler = $client->request('GET', '/article/add/');        
        // Czy status odpowiedzi http jest 200
        $this->assertTrue($client->getResponse()->getStatusCode() == '200' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "not 200!!!!");  
        // Sprawdza czy na stronie pojawia się wyrażnie Nowy 
        $this->assertTrue($crawler->filter('html:contains("nowy")')->count() > 0);                 
        // Wypełnia formularz
        $form = $crawler->selectButton('submit')->form();
        $form['articleform[title]'] = 'some test title ';
        $form['articleform[content]'] = 'some test content';
        // Zatwierdza formualrz 
        $crawler = $client->submit($form);         
        // Czy status odpowiedzi http jest równy 302
        $this->assertTrue($client->getResponse()->getStatusCode() == '302' ,
                "Response code is: ".$client->getResponse()->getStatusCode().
                "not 302!!!!");              
         $cont = $client->getResponse()->getContent();                        
         $crawler = $client->request('GET', $cont);
         // Czy na stronie po przekierowaniu mamy napis "Poprawnie"
         $this->assertTrue($crawler->filter('html:contains("Poprawnie")')->count() > 0);      
    }
            
}
