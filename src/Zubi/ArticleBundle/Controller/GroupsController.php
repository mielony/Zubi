<?php

namespace Zubi\ArticleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Zubi\ArticleBundle\Entity\ArticleGroups;
use Zubi\ArticleBundle\Form\Article\ArticleGroupsForm;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


class GroupsController extends Controller{
    public function indexAction() {      
         $em = $this->getDoctrine()->getEntityManager();            
         $articleGroups = $em->getRepository('ZubiArticleBundle:ArticleGroup')->findAll();
         return $this->render('ZubiArticleBundle:Groups:index.html.twig',
                 array('articleGroups' => $articleGroups)                 
                 );
    }
    
    public function deleteAction($id) {        
         return $this->render('ZubiArticleBundle:Groups:delete.html.twig',
                   array (
                    'id' => $id)
                 );
        
    }
    
    public function editAction($id) {        
        return $this->render('ZubiArticleBundle:Groups:edit.html.twig',
                   array (
                    'id' => $id)
                 );
    }
}
