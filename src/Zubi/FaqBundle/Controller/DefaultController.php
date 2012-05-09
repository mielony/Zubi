<?php
namespace Zubi\FaqBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Zubi\FaqBundle\Entity\Faq;
use Zubi\FaqBundle\Entity\Status_widocznosci;
use Zubi\FaqBundle\Form\Faq\FaqForm;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/* 
 * klasa kontrolera odpowiadająca za FAQ
 * 
 * editAction: Metoda-AKCJA odpowiadająca za edycję FAQ
 *     Jako argumenty przyjmuje 
 *       $request ponieważ zawiera formularz
 *       $id - numer edytowanego FAQ (FAQ.id)
 * 
 * deleteAction: Metoda-AKCJA odpowiadająca za usunięcie FAQ
 *     Jako argumenty przyjmuje      
 *       $id - numer edytowanego FAQ (FAQ.id)
 *  
 * indexAction: Metoda-AKCJA odpowiadająca za wyświetlenie listy FAQ oraz dodawania FAQ
 *     Jako argumenty przyjmuje      
 *       $request ponieważ zawiera formularz dodawania FAQ
 *   
 */
class DefaultController extends Controller
{
    protected $user;   
    
    /*
     * 
     * Metoda-AKCJA odpowiadająca za edycję FAQ
     * Jako argumenty przyjmuje 
     *     $request ponieważ zawiera formularz
     *     $id - numer edytowanego FAQ (FAQ.id)
     * 
     */
    public function editAction(Request $request, $id) {
        // Określenie linku powrotnego, potem przekazany do warstwy prezetnacji
        $backLink = $this->generateUrl('ZubiFaqBundle_homepage');
        $newFaq = new Faq();
        // Połaczenie z Doctrine (BD)
        $em = $this->getDoctrine()->getEntityManager();    
        // pobranie edytowanego FAQ z BD
        $editedFaq = $this->getDoctrine()
                    ->getRepository('ZubiFaqBundle:Faq')
                    ->findOneById($id);
        //jeśli dane zostały pobrane
        if ($editedFaq) {
            //generacja formularza FAQ oraz wypełenienie go pobranymi danymi
            $form = $this->createForm(new FaqForm(), $editedFaq);          
            //jeśli strona nie została wywołana w odpowiedzi na zatwierdzenie fromularza
            if($request->getMethod() != 'POST') {                         
                // następuje wyświetlenie strony z formularzem oraz linkiem
                return $this->render('ZubiFaqBundle:Default:edit.html.twig',
                        array ('form' => $form->createView(),
                                'backLink' => $backLink ));
            }
            //jeśli strona została wywołana w odpowiedzi na zatwierdzenie fromularza
            else {                    
                // wypełnij formularz danymi pobranymi przez przeglądarkę
                // z formularza zatwierdzonego
                $form->bindRequest($request); 
                // przeprowadzenie walidacji danych
                $validator = $this->get('validator');
                $errors = $validator->validate($editedFaq);
                // jesli nie ma błędów
                if (count($errors) < 1) 
                {                     
                    // zapisz odpowiedni status widoczności - zapisujemy ID a
                    // nie nazwę
                    $sw = $this->getDoctrine()
                            ->getRepository('ZubiFaqBundle:Status_widocznosci')
                            ->findOneById($editedFaq->getStatusWidocznosci()->getId());
                    $editedFaq->setStatusWidocznosci($sw);                
                    // zapisz dane
                    $em->flush();
                    // przekrż wiadomość do następnej strony o sukcesie
                    $this->get('session')->setFlash('notice', 'Sukces edycji FAQ pt: "'.$editedFaq->getTresc().'"');
                    // przekierowanie na stronę z listą FAQ
                    return $this->redirect($this->generateUrl('ZubiFaqBundle_homepage'));
                }                     
            }
        }
        else{
            // błąd. Została wywołana strona edycji FAQ, którego nie ma
            // zrobienie informacji (error)
            $this->get('session')->setFlash('errorMsg', 'Nie ma czego edytować, nie ma FAQ o id: '.$id.'!');
            // przekierowanie na stronę z listą FAQ
            return $this->redirect($this->generateUrl('ZubiFaqBundle_homepage'));
        }                
      }
    
    /*
     * 
     * Metoda-AKCJA odpowiadająca za usunięcie FAQ
     * Jako argumenty przyjmuje      
     *     $id - numer edytowanego FAQ (FAQ.id)
     * 
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getEntityManager();                               
        //pobieramy z bazy FAQ do skasowania.
        $delFaq = $this->getDoctrine()
                ->getRepository('ZubiFaqBundle:Faq')
                ->findOneById($id);                 
        if ($delFaq) {
            // kasujemy FAQ 
            $em->remove($delFaq);
            $em->flush();
            // przekierowanie na index z FAQ
            $this->get('session')->setFlash('notice', 'Skasowałeś FAQ pt: "'.$delFaq->getTresc().'"');
            return $this->redirect($this->generateUrl('ZubiFaqBundle_homepage'));                       
            // TODO: Może jakieś pytanie czy na 100% usunąć? Na razie nie ma
        }
        else {
            // błąd. Została wywołana strona usunięcia FAQ, którego nie ma
            // zrobienie informacji (error)
            $this->get('session')->setFlash('errorMsg', 'Nie ma czego kasowac, nie ma FAQ o id: '.$id.'!');
            // przekierowanie na stronę z listą FAQ
            return $this->redirect($this->generateUrl('ZubiFaqBundle_homepage'));
        }          
    }    
    
    /*
     * 
     * Metoda-AKCJA odpowiadająca za wyświetlenie listy FAQ oraz dodawania FAQ
     * Jako argumenty przyjmuje      
     *     $request ponieważ zawiera formularz dodawania FAQ
     * 
     */    
    public function indexAction(Request $request)
    {
     $newFaq = new Faq();
     // połaczenie z Doctrine (DB)
     $em = $this->getDoctrine()->getEntityManager();    
     // stworzenie pustego formularza do dodawania FAQ
     $form = $this->createForm(new FaqForm(), $newFaq);      
     //jeśli strona wyswietla się po przesłaniu formularza w POST
     //trzeba spróbować dodać dane do bazy danych
     if($request->getMethod() == 'POST') {          
        // wypełnij formularz danymi pobranymi przez przeglądarkę
        // z formularza zatwierdzonego
        $form->bindRequest($request);      
        // przeprowadź walidację
        $validator = $this->get('validator');
        //zapisz błedy
        $errors = $validator->validate($newFaq);
        //jeśli przesyłane dane są poprawne
        //dodajemy je do bazy oraz czyścimy formularz.
        if (count($errors) < 1) {                                                                          
                $sw = $this->getDoctrine()
                        ->getRepository('ZubiFaqBundle:Status_widocznosci')
                        ->findOneById($newFaq->getStatusWidocznosci()->getId());
                $newFaq->setStatusWidocznosci($sw);
                $em->persist($newFaq );
                $em->flush();
                // przekrż wiadomość do następnej strony o sukcesie
                $this->get('session')->setFlash('notice', 'Poprawnie dodałeś nowe FAQ.');
                //po poprawnym dodaniu danych z formularza, chcemy mieć go pustego.                
                $newFaq = new Faq();
                $form = $this->createForm(new FaqForm(), $newFaq);                 
        }                     
     }       
     //pobieramy z bazy wszystkie faq     
     $faqs = $em->getRepository('ZubiFaqBundle:Faq')->findAll();     
     // dla każdego faq tworzę link do jego usunięcia.
     $a = 0;
     $delLinks[0]="";
     $editLinks[0] = "";
     // stworzenie dla każdego FAQ linków do ich edycji i usuwania
     foreach ($faqs as $faq) {
         $delLinks[$a] = $this->generateUrl('ZubiFaqBundle_delete', array ('id' => $faq->getId() ));
         $editLinks[$a] = $this->generateUrl('ZubiFaqBundle_edit', array ('id' => $faq->getId() ));
         $a++;                 
     }     
     $this->user = $this->get('security.context')->getToken()->getUser();
     // następuje wyświetlenie strony z formularzem, listą FAQ oraz linkami    
     return $this->render('ZubiFaqBundle:Default:index.html.twig', 
                array('faqs' => $faqs, 
                      'delLinks' => $delLinks,
                      'editLinks' => $editLinks,         
                      'form' => $form->createView()));
    }    
}
