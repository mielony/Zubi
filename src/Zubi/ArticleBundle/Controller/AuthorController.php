<?php

namespace Zubi\ArticleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Zubi\UserBundle\Entity\Osoba;
use Zubi\ArticleBundle\Form\Person\PersonForm;


/* 
 * klasa kontrolera odpowiadająca za Artykuły
 * 
 * addAction: Metoda-AKCJA odpowiadająca za stworzenie strony z formularzem 
 *               do dodawania nowych autorów
 *               argumenty:      
 *                 $request ponieważ zawiera formularz
 * 
 */
class AuthorController extends Controller{

    //po dodaniu autora wraca do edyji artykyłu
    public function addAction(Request $request, $id) {           
        $em = $this->getDoctrine()->getEntityManager(); 
        $newOsoba = new Osoba(); 
        $form = $this->createForm(new PersonForm(), $newOsoba);                
         if($request->getMethod() == 'POST') {          
            $form->bindRequest($request);         
            $validator = $this->get('validator');
            $errors = $validator->validate($newOsoba);            
            if (count($errors) < 1) {                                                                                                          
                $em->persist($newOsoba);
                $em->flush();
                $this->get('session')->setFlash('notice', 'Poprawnie dodałeś nowego autora.');
                if ($id <> 0 ){                         
                    return $this->redirect($this->generateUrl('ZubiArticleBundle_edit', array ('id' => $id)));
                }
                else {
                    return $this->redirect($this->generateUrl('ZubiArticleBundle_add'));
                }
            }            
         }         
        return $this->render('ZubiArticleBundle:Author:add.html.twig',
                array('id' => $id, 
                      'form' => $form->createView()
                        ));
    }
}
