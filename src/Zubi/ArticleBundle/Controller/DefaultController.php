<?php

namespace Zubi\ArticleBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Zubi\ArticleBundle\Entity\Article;
use Zubi\ArticleBundle\Form\Article\ArticleForm;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Zubi\UserBundle\Entity\Osoba;

/* 
 * klasa kontrolera odpowiadająca za Artykuły
 * 
 * indexAction : Metoda-AKCJA odpowiadająca za pobranie listy artykułów i przekazanie 
 *               jej do warstwy prezentacji.
 * 
 * showArticleAction: Metoda-AKCJA odpowiadająca za pobranie i wyświetlenie 
 *               konkretnego artykułu
 *               argumenty: 
 *                 $id - numer artykułu do wyświetlenia (Artykuły.id)         
 * 
 * addAction: Metoda-AKCJA odpowiadająca za stworzenie strony z formularzem 
 *               do dodawania nowych artykułów
 *               argumenty:      
 *                 $request ponieważ zawiera formularz
 * 
 * deleteAction: Metoda-AKCJA odpowiadająca za usuwanie danych artykułu
 *               argumenty: 
 *                 $id - numer artykułu  (Artykuły.id)
 *      
 * editAction: Metoda-AKCJA odpowiadająca za edycje artykułu
 *               argumenty: 
 *                 $id - numer artykułu  do edycji(Artykuły.id)
 *                 $request ponieważ zawiera formularz
 * 
 */

class DefaultController extends Controller
{
    /* 
     * Metoda-AKCJA odpowiadająca za pobranie listy artykułów i przekazanie 
     *      jej do warstwy prezentacji.
     */
    public function indexAction() {    
        // połączenie z Doctrine (BD)
        $em = $this->getDoctrine()->getEntityManager();            
        // pobralnie artykułów, sortowanie po grupie artukułu
        $articles = $em->createQuery('SELECT ar FROM ZubiArticleBundle:Article ar ORDER BY ar.groupId ASC')
            ->getResult();
        // przekazanie pobranej listy do warstwy prezentacji
        return $this->render('ZubiArticleBundle:Default:index.html.twig', array (
                    'articles' => $articles                  
                ));
    }
    
    /* 
     * 
     * Metoda-AKCJA odpowiadająca za pobranie i wyświetlenie konkretnego artykułu
     *    argumenty: 
     *      $id - numer artykułu do wyświetlenia (Artykuły.id)
     * 
     */
    public function showArticleAction($id){       
        // połączenie z Doctrine (BD)
        $em = $this->getDoctrine()->getEntityManager();            
        // pobranie danych artyułu o id = $id
        $article = $em->getRepository('ZubiArticleBundle:Article')->findOneById($id);   
        //jeśli nie znaleziono artykułu, więc redirect na listę artyk
        if (!$article) {
            // przekazanie infromacji (error) o złym wywołaniu strony
            $this->get('session')->setFlash('errorMsg', 'Próbowałeś wyświetlić nieistniejący artykuł');
            // przekierowanie na stronę listy artykułów
            return $this->redirect($this->generateUrl('ZubiArticleBundle_homepage'));
        }
        //jest artykuł więc dalej pobieramy autora, twórcę, kategorię 
        $author = $em->getRepository('ZubiUserBundle:Osoba')->findOneById($article->getAuthorId());
        // jeśli autor został wykasowany z bazy tworzymy pustą instancję
        if (!$author) $author = new Osoba();        
        // pobieramy dane twórcy artykułu
        $creator = $em->getRepository('ZubiUserBundle:User')->findOneById($article->getUserId());
        // pobieramy nazwę kategori artykułu
        $category = $em->getRepository('ZubiArticleBundle:ArticleGroup')->findOneById($article->getGroupId());
        //przekazujemy pobrane dane do warstwy prezentacji
        return $this->render('ZubiArticleBundle:Default:showArticle.html.twig',
                array (
                    'id' => $id,
                    'article' => $article,
                    'author' => $author,
                    'creator' => $creator,
                    'category' => $category,
                    'backLink' => $this->generateUrl('ZubiArticleBundle_homepage')
                ));
    } 
    
    /* 
     * 
     * Metoda-AKCJA odpowiadająca za stworzenie strony z formularzem do dodawania
     *    nowych artykułów
     *    argumenty: 
     *      $id - numer artykułu do wyświetlenia (Artykuły.id)
     * 
     */
    public function addAction(Request $request)
    {        
        $newArticle = new Article();
        $em = $this->getDoctrine()->getEntityManager();    
        $form = $this->createForm(new ArticleForm(), $newArticle);                                
        if($request->getMethod() == 'POST') {                   
            $form->bindRequest($request);                     
            $validator = $this->get('validator');
            $errors = $validator->validate($newArticle);          
            // jeśli przesyłane dane są poprawne        
            if (count($errors) < 1) {                                                                                                                                                      
                    //sprawdzamy Id usera i ustawiamy do zapsiania w artukule
                    $newArticle->setUserId($this->get("security.context")->getToken()->getUser()->getId());
                    //dodajemy je do bazy
                    $em->persist($newArticle);                                        
                    $em->flush();
                    // generujemy komunikat o sukcesie do wyświetlenia
                    $this->get('session')->setFlash('notice', 'Poprawnie dodałeś artykuł');
                    // przekierowujemy stronę na listę artykułów
                    return $this->redirect($this->generateUrl('ZubiArticleBundle_homepage'));
            }     
            // jeśli dane nie sa poprawne wyświetlamy formularz, wypełniony danymi
            return $this->render('ZubiArticleBundle:Default:add.html.twig',
                                array (                   
                                    'form' => $form->createView()                    
                                ));              
        }
        // jeśli dane nie sa poprawne wyświetlamy formularz, z pustymi danymi
        return $this->render('ZubiArticleBundle:Default:add.html.twig',
                                array (                   
                                    'form' => $form->createView()                    
                                ));     
    }    
        
    /* 
     * 
     * Metoda-AKCJA odpowiadająca za usuwanie danych artykułu
     *    argumenty: 
     *      $id - numer artykułu  (Artykuły.id)
     * 
     */
    public function deleteAction($id) {
        // połaczenie z doctrine
        $em = $this->getDoctrine()->getEntityManager();                                       
        // pobranie danych z bazy. Article.id = $id
        $delArt = $this->getDoctrine()
                ->getRepository('ZubiArticleBundle:Article')
                ->findOneById($id);         
        // jesli jest co kasować
        if ($delArt) {          
            //należy to skasować
            $em->remove($delArt);
            $em->flush();
            // generacja komunikatu o sukcesie
            $this->get('session')->setFlash('notice', 'Sukses - Skasowałeś Artykuł pt: "'.$delArt->getTitle().'"');
            // przekierowanie na listę artykułów
            return $this->redirect($this->generateUrl('ZubiArticleBundle_homepage'));                        
            // TODO: Może jakieś pytanie czy na 100% usunąć? Na razie nie ma
        }
        else {
            // generacja komunikatu o błędzie
            $this->get('session')->setFlash('errorMsg', 'Nie ma czego kasowac, nie ma artukułu o id: '.$id.'!');
            // przekierowanie na listę artykułów
            return $this->redirect($this->generateUrl('ZubiArticleBundle_homepage'));
        }          
    }    
    
    /* 
     * 
     * Metoda-AKCJA odpowiadająca za edycje artykułu
     *    argumenty: 
     *      $id - numer artykułu  do edycji(Artykuły.id)
     * 
     */
    public function editAction(Request $request, $id) {                
        // połaczenie z doctrine (BD)
        $em = $this->getDoctrine()->getEntityManager();    
        // pobranie artykułu do edycji
        $editedArt = $this->getDoctrine()
                    ->getRepository('ZubiArticleBundle:Article')
                    ->findOneById($id);
        // jeśli są dane
        if ($editedArt) {
            // wypełnienie nimi formularza
            $form = $this->createForm(new ArticleForm(), $editedArt);          
            // jeśli strona nie jest wywołana po zatwierdzeniu formularza
            if($request->getMethod() != 'POST') {               
                // wypełnienie formularza danymi 
                $form = $this->createForm(new ArticleForm(), $editedArt);  
                // wyświetlenie strony z formularzem i danymi do edycji (z BD)
                return $this->render('ZubiArticleBundle:Default:edit.html.twig',
                        array ('form' => $form->createView(),
                                'id' => $id                        
                        ));
            }
            // jeśli strona wywołana po zatwierdzeniu formularza
            else {        
                // pobranie danych podanych przez użytkownika
                $form->bindRequest($request);         
                // walidacja
                $validator = $this->get('validator');               
                $errors = $validator->validate($editedArt);
                //sprawdzenie błędów, jesli nie ma
                if (count($errors) < 1) 
                {                                 
                    // zapisanie danych
                    $em->flush();
                    // wygenerowanie komunikatu o sukcesie
                    $this->get('session')->setFlash('notice', 'Poprawnie edytowałeś artykuł');                                        
                    // wyświetlenie listy artykułów
                    return $this->redirect($this->generateUrl('ZubiArticleBundle_homepage'));
                }                     
            }
        }
        // brak danych
        else {
            // wygenerowanie komunikatu o błędzie
            $this->get('session')->setFlash('errorMsg', 'Nie ma czego edytować, nie ma Artykułu o id: '.$id.'!');
            // przekierowanie na stronę z lista artykułów
            return $this->redirect($this->generateUrl('ZubiArticleBundle_homepage'));
        }                
      }
}
