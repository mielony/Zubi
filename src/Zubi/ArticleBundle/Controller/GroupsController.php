<?php

namespace Zubi\ArticleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Zubi\ArticleBundle\Entity\ArticleGroup;
use Zubi\ArticleBundle\Entity\Article;
use Zubi\ArticleBundle\Form\ArticleGroup\ArticleGroupForm;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


class GroupsController extends Controller{
    public function indexAction(Request     $request) {      
         $em = $this->getDoctrine()->getEntityManager();                                       
         $newArticleGr = new ArticleGroup();         
         $form = $this->createForm(new ArticleGroupForm(), $newArticleGr);              
         if($request->getMethod() == 'POST') {          
            $form->bindRequest($request);         
            $validator = $this->get('validator');
            $errors = $validator->validate($newArticleGr);            
            if (count($errors) < 1) {                                                                                                          
                $em->persist($newArticleGr );
                $em->flush();
                $this->get('session')->setFlash('notice', 'Poprawnie dodałeś nową grupę art.');
                $newArticleGr = new ArticleGroup();         
                $form = $this->createForm(new ArticleGroupForm(), $newArticleGr);                  
            }
         }
        $articleGroups = $em->getRepository('ZubiArticleBundle:ArticleGroup')->findAll();
        return $this->render('ZubiArticleBundle:Groups:index.html.twig',
                 array('articleGroups' => $articleGroups,
                       'form' => $form->createView()    )                 
                 );
    }
    
    public function deleteAction($id) {        
        $em = $this->getDoctrine()->getEntityManager();                                       
        $delArtGr = $em->getRepository('ZubiArticleBundle:ArticleGroup')
                ->findOneById($id);                
        if ($em->getRepository('ZubiArticleBundle:Article')
                ->findOneByGroupId($id)) {        
            $this->get('session')->setFlash('errorMsg', 'Nie możesz usunąc grupy. Posiada artykuły!');
            return $this->redirect($this->generateUrl('ZubiArticleBundle_groups'));
        }
        if ($delArtGr) {            
            $em->remove($delArtGr);
            $em->flush();            
            $this->get('session')->setFlash('notice', 'Skasowałeś grupę dla artykuł pt: "'.$delArtGr->getName().'"');
            return $this->redirect($this->generateUrl('ZubiArticleBundle_groups'));
                        
            // TODO: Może jakieś pytanie czy na 100% usunąć? Na razie nie ma
        }
        $this->get('session')->setFlash('errorMsg', 'Nie ma czego kasowac, nie ma artukułu o id: '.$id.'!');
        return $this->redirect($this->generateUrl('ZubiArticleBundle_groups'));       
    }
    
    public function editAction(Request $request, $id) {     
        $newArticleGr = new ArticleGroup();   
        $em = $this->getDoctrine()->getEntityManager();    
        $editedArticleGr = $this->getDoctrine()
                    ->getRepository('ZubiArticleBundle:ArticleGroup')
                    ->findOneById($id);
        if ($editedArticleGr ) {
           $form =  $this->createForm(new ArticleGroupForm(), $editedArticleGr);                  
             if($request->getMethod() != 'POST') {                                        
                return $this->render('ZubiArticleBundle:Groups:edit.html.twig',
                        array ('form' => $form->createView(),
                               'id' => $id
                               ));
            }
            else {                    
                $form->bindRequest($request);         
                $validator = $this->get('validator');
                $errors = $validator->validate($editedArticleGr);
                if (count($errors) < 1) 
                {                                                                                                   
                    $em->flush();
                    $this->get('session')->setFlash('notice', 'Sukces edycji grupy nowa nazwa: "'.$editedArticleGr->getName().'"');
                    return $this->redirect($this->generateUrl('ZubiArticleBundle_groups'));
                }                     
            }
        }
        else{
            $this->get('session')->setFlash('errorMsg', 'Nie ma czego edytować, nie ma grupy o id: '.$id.'!');
            return $this->redirect($this->generateUrl('ZubiArticlesBundle_groups'));
        }                
        
        
        
        
        
        return $this->render('ZubiArticleBundle:Groups:edit.html.twig',
                   array (
                    'id' => $id)
                 );
    }
}
