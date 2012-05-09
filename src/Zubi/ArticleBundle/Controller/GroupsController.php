<?php

namespace Zubi\ArticleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Zubi\ArticleBundle\Entity\ArticleGroup;
use Zubi\ArticleBundle\Entity\Article;
use Zubi\ArticleBundle\Form\ArticleGroup\ArticleGroupForm;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/* 
 *
 * klasa kontrolera odpowiadająca za Grupy Artykułów
 * 
 * indexAction : Metoda-AKCJA odpowiadająca za pobranie listy grup artykułów 
 *                   i przekazanie jej do warstwy prezentacji oraz dodawanie 
 *                   nowych grup.
 *              argumenty:
 *                $request ponieważ zawiera formularz
 * 
 * deleteAction: Metoda-AKCJA odpowiadająca za usuwanie danych artykułu      
 *              argumenty: 
 *                 $id - numer grupy       
 * editAction: Metoda-AKCJA odpowiadająca za stworzenie strony z formularzem 
 *                 do dodawania nowych grup dla artykułów.
 *                 Metoda wywoływana przez stronę dodawania artykułów
 *                 w razie gdyby brakowało grupy
 *              argumenty: 
 *                 $request ponieważ zawiera formularz
 * 
 */
 
class GroupsController extends Controller{
    
    /* 
     * Metoda-AKCJA odpowiadająca za pobranie listy grup artykułów i przekazanie 
     *      jej do warstwy prezentacji oraz dodawanie nowych grup.
     *      argumenty:
     *         $request ponieważ zawiera formularz
     */
    public function indexAction(Request   $request) {      
         // połączenie z doctrine (BD)
         $em = $this->getDoctrine()->getEntityManager();                                                
         $newArticleGr = new ArticleGroup();         
         // stworzenie pustego formularza dla Grupy Artykułów
         $form = $this->createForm(new ArticleGroupForm(), $newArticleGr);              
         // jeśli wywołanie strony jest po zatwierdzeniu formularza
         if($request->getMethod() == 'POST') {          
            // pobieramy dane przekazne przez formularz
            $form->bindRequest($request);         
            // walidacja
            $validator = $this->get('validator');
            $errors = $validator->validate($newArticleGr);            
            // jeśli brak błędów
            if (count($errors) < 1) {                                                                                                          
                // zapisujemy dane 
                $em->persist($newArticleGr );
                $em->flush();
                //generujemy komunikat o sukcesie
                $this->get('session')->setFlash('notice', 'Poprawnie dodałeś nową grupę art.');
                // czyścimy formularz
                $newArticleGr = new ArticleGroup();                         
                $form = $this->createForm(new ArticleGroupForm(), $newArticleGr);                  
            }
         }
         // pobieramy listę grup
        $articleGroups = $em->getRepository('ZubiArticleBundle:ArticleGroup')->findAll();
        // formualrz oraz listę przekazujemy do warstwy prezentacji
        return $this->render('ZubiArticleBundle:Groups:index.html.twig',
                 array('articleGroups' => $articleGroups,
                       'form' => $form->createView()    )                 
                 );
    }
    
     /*
      *  Metoda-AKCJA odpowiadająca za usuwanie danych artykułu      
      *               argumenty: 
      *                 $id - numer grupy       
      */
    public function deleteAction($id) {        
        // połączenie z doctrine (BD)
        $em = $this->getDoctrine()->getEntityManager();        
        // znalezienie grupy o zadanym id
        $delArtGr = $em->getRepository('ZubiArticleBundle:ArticleGroup')
                ->findOneById($id);      
        // jeśli id tej grupy znajduje się w którymś z artykułów, nie możemy usunąć
        if ($em->getRepository('ZubiArticleBundle:Article')
                ->findOneByGroupId($id)) {        
            // generujemy komunikat o błędzie
            $this->get('session')->setFlash('errorMsg', 'Nie możesz usunąc grupy. Posiada artykuły!');
            // przekierowanie na listę grup
            return $this->redirect($this->generateUrl('ZubiArticleBundle_groups'));
        }
        // jeśli można kasować (są dane)
        if ($delArtGr) {            
            // usuwany grupę
            $em->remove($delArtGr);
            $em->flush();   
            // tworzymy komunikat o sukcesie
            $this->get('session')->setFlash('notice', 'Skasowałeś grupę dla artykuł pt: "'.$delArtGr->getName().'"');
            // przekierowanie na listę grup
            return $this->redirect($this->generateUrl('ZubiArticleBundle_groups'));                        
            // TODO: Może jakieś pytanie czy na 100% usunąć? Na razie nie ma
        }
        // jeśli nie ma danych do usunięcia
        // generujemy komunikat o błędzie
        $this->get('session')->setFlash('errorMsg', 'Nie ma czego kasowac, nie ma artukułu o id: '.$id.'!');
        // przekierowanie na listę grup
        return $this->redirect($this->generateUrl('ZubiArticleBundle_groups'));       
    }
    
     /*
      *  editAction: Metoda-AKCJA odpowiadająca za edycję grupy
      *     Jako argumenty przyjmuje 
      *       $request ponieważ zawiera formularz
      *       $id - numer id grupy
      * 
      */
    public function editAction(Request $request, $id) {             
        $newArticleGr = new ArticleGroup();   
        // połączenie z doctrine (DB)
        $em = $this->getDoctrine()->getEntityManager();    
        // pobranie danych grupy do edycji
        $editedArticleGr = $this->getDoctrine()
                    ->getRepository('ZubiArticleBundle:ArticleGroup')
                    ->findOneById($id);
        // jeśli są dane (zostały pobrane)
        if ($editedArticleGr ) {
           // wypełniamy nimi formularz
           $form =  $this->createForm(new ArticleGroupForm(), $editedArticleGr);                  
           // jeśli wywołanie strony nie nastąpiło po zatwierdzeniu formularza
           if($request->getMethod() != 'POST') {                                        
                // przekazujemy formularz do wyświetlenia przez warstwę prezentacji
                return $this->render('ZubiArticleBundle:Groups:edit.html.twig',
                        array ('form' => $form->createView(),
                               'id' => $id
                               ));
            }
            // jeśli wywołanie po zatwierdzeniu formularza
            else {                    
                //pobieramy dane przekazane przez formularz
                $form->bindRequest($request);         
                //walidacja
                $validator = $this->get('validator');
                //sprawdzenie błędów
                $errors = $validator->validate($editedArticleGr);
                // brak błedów
                if (count($errors) < 1) 
                {             
                    // zapisujemy, tworzymy komunikat o sukcesie i wyświetlamy listę grup
                    $em->flush();
                    $this->get('session')->setFlash('notice', 'Sukces edycji grupy nowa nazwa: "'.$editedArticleGr->getName().'"');
                    return $this->redirect($this->generateUrl('ZubiArticleBundle_groups'));
                }                     
            }
        }
        // nie ma czego edytować - nie ma id
        else{
            // tworzymy komuniakt o błedzie
            $this->get('session')->setFlash('errorMsg', 'Nie ma czego edytować, nie ma grupy o id: '.$id.'!');
            // przechodzimy do strony z listą grup
            return $this->redirect($this->generateUrl('ZubiArticleBundle_groups'));
        }                                             
    }
    
    /*  Metoda-AKCJA odpowiadająca za stworzenie strony z formularzem 
     *               do dodawania nowych grup dla artykułów.
     *               Metoda wywoływana przez stronę dodawania artykułów
     *               w razie gdyby brakowało grupy
     *               argumenty: 
     *                 $request ponieważ zawiera formularz
     * 
     */
    public function addAction(Request $request) {      
         // połączenie z doctrine 
         $em = $this->getDoctrine()->getEntityManager();                                       
         // nowy artykół - pusty
         $newArticleGr = new ArticleGroup();         
         // formualrz pusty
         $form = $this->createForm(new ArticleGroupForm(), $newArticleGr);              
         // jeśli strona wywołana jest po zatwierdzeniu formularza
         if($request->getMethod() == 'POST') {          
            // pobranie danych pobranych z zatwierdzonego formularza
            $form->bindRequest($request);         
            // walidacja
            $validator = $this->get('validator');
            // sprawdzenie błędów
            $errors = $validator->validate($newArticleGr);            
            // jeśli brak błędów
            if (count($errors) < 1) {                                                                                                          
                // zapisanie danych
                $em->persist($newArticleGr );
                $em->flush();
                // stworzenie komuniatu o sukcesie
                $this->get('session')->setFlash('notice', 'Poprawnie ..dodałeś nową grupę art.');
                // przekierowanie na stronę tworzenia artykułów
                return  $response = $this->forward('ZubiArticleBundle:Default:add' );                 
            }
         }          
        // jeśli strona wywołana nie poprzez zatwierdzenie formularza
        // przekazanie formularza do wawrstwy prezentacji
        return $this->render('ZubiArticleBundle:Groups:add.html.twig',array(
                           'form' => $form->createView()    )                 
                 );
    }     
}
