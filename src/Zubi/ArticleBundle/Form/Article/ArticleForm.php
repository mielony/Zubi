<?php
namespace Zubi\ArticleBundle\Form\Article; 

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;


class ArticleForm extends AbstractType {

    
    public function buildForm(FormBuilder $builder, array $options) {
        //  $builder -> add('group', 'entity', array('class' => 'Zubi\ArticleBundle\Entity\ArticleGroup'));
          $builder -> add('title', 'text' );
          $builder -> add('content', 'textarea');          
          $builder -> add('articleGroup', 'entity', array(
                                        'class' => 'Zubi\ArticleBundle\Entity\ArticleGroup' )
                        );     
      
          $builder -> add('author', 'entity', array(
                                        'class' => 'Zubi\UserBundle\Entity\Osoba')
                        );
          $builder -> add('StatusWidocznosci', 'entity', array(
                        'class' => 'Zubi\FaqBundle\Entity\Status_widocznosci'
                        //,'property' => 'nazwa'
                    ));
    }
    
    public function getName() {
        return 'articleform';
    }
}
