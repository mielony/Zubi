<?php

namespace Zubi\ArticleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Zubi\ArticleBundle\Entity\Article;
use Zubi\ArticleBundle\Form\Article\ArticleForm;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


class DefaultController extends Controller
{
    
    public function indexAction() {        
        $em = $this->getDoctrine()->getEntityManager();            
        $articles = $em->getRepository('ZubiArticleBundle:Article')->findAll();     
        return $this->render('ZubiArticleBundle:Default:index.html.twig',
                array (
                    'articles' => $articles                  
                ));
    }
    
    public function showArticleAction($id){       
        $em = $this->getDoctrine()->getEntityManager();            
        $article = $em->getRepository('ZubiArticleBundle:Article')->findOneById($id);   
        //jeśli nie znaleziono artykułu, więc redirect na listę artyk
        if (!$article) {
            $this->get('session')->setFlash('errorMsg', 'Próbowałeś wyświetlić nieistniejący artykuł');
            return $this->redirect($this->generateUrl('ZubiArticleBundle_homepage'));
        }
        //jes artykuł więc dalej pobieramy autora, twórcę, kategorię 
        $author = $em->getRepository('ZubiUserBundle:Osoba')->findOneById($article->getAuthorId());
        $creator = $em->getRepository('ZubiUserBundle:User')->findOneById($article->getUserId());
        $category = $em->getRepository('ZubiArticleBundle:ArticleGroup')->findOneById($article->getGroupId());
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
    
    
    public function addAction(Request $request)
    {        
        $newArticle = new Article();
        $em = $this->getDoctrine()->getEntityManager();    
        $form = $this->createForm(new ArticleForm(), $newArticle);                        
        //jeśli przesyłane dane są poprawne
        //dodajemy je do bazy oraz czyścimy formularz.
        if($request->getMethod() == 'POST') {               
            $form->bindRequest($request);                     
            $validator = $this->get('validator');
            $errors = $validator->validate($newArticle);          
            if (count($errors) < 1) {                                                                                                                                  
                    $newArticle->setUserId($this->get("security.context")->getToken()->getUser()->getId());
                    $em->persist($newArticle);
                    $em->flush();
                    $this->get('session')->setFlash('notice', 'Poprawnie dodałeś artykuł');
                    return $this->redirect($this->generateUrl('ZubiArticleBundle_homepage'));
            }     
            return $this->render('ZubiArticleBundle:Default:add.html.twig',
                                array (                   
                                    'form' => $form->createView()                    
                                )
                            );              
        }
        return $this->render('ZubiArticleBundle:Default:add.html.twig',
                                array (                   
                                    'form' => $form->createView()                    
                                )
                            );
     
    }    
    
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getEntityManager();                                       
        $delArt = $this->getDoctrine()
                ->getRepository('ZubiArticleBundle:Article')
                ->findOneById($id);         
        if ($delArt) {            
            $em->remove($delArt);
            $em->flush();
            // przekierowanie na index z FAQ
            $this->get('session')->setFlash('notice', 'Sukses - Skasowałeś Artykuł pt: "'.$delArt->getTitle().'"');
            return $this->redirect($this->generateUrl('ZubiArticleBundle_homepage'));
                        
            // TODO: Może jakieś pytanie czy na 100% usunąć? Na razie nie ma
        }
        else {
            $this->get('session')->setFlash('errorMsg', 'Nie ma czego kasowac, nie ma artukułu o id: '.$id.'!');
            return $this->redirect($this->generateUrl('ZubiArticleBundle_homepage'));
        }          
    }
    
    
    public function editAction(Request $request, $id) {        
        $newArt = new Article();
        $em = $this->getDoctrine()->getEntityManager();    
        $editedArt = $this->getDoctrine()
                    ->getRepository('ZubiArticleBundle:Article')
                    ->findOneById($id);
        if ($editedArt) {
            $form = $this->createForm(new ArticleForm(), $editedArt);          
            if($request->getMethod() != 'POST') {                         
                $form = $this->createForm(new ArticleForm(), $editedArt);  
                return $this->render('ZubiArticleBundle:Default:edit.html.twig',
                        array ('form' => $form->createView(),
                                'id' => $id                        
                        ));
            }
            else {                    
                $form->bindRequest($request);         
                $validator = $this->get('validator');
                $errors = $validator->validate($editedArt);
                if (count($errors) < 1) 
                {                                                                                              
                    $em->flush();
                    $this->get('session')->setFlash('notice', 'Poprawnie edytowałeś artykuł');                                        
                    return $this->redirect($this->generateUrl('ZubiArticleBundle_homepage'));
                }                     
            }
        }
        else{
            $this->get('session')->setFlash('errorMsg', 'Nie ma czego edytować, nie ma Artykułu o id: '.$id.'!');
            return $this->redirect($this->generateUrl('ZubiArticleBundle_homepage'));
        }                
      }
}
