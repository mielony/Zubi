<?php
namespace Zubi\ArticleBundle\Form\ArticleGroup; 

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;


class ArticleGroupForm extends AbstractType {
    
    public function buildForm(FormBuilder $builder, array $options) {      
          $builder -> add('name', 'text' );          
    }
    
    public function getName() {
        return 'articleGroupform';
    }
}
