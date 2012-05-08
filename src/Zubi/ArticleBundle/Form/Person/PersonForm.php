<?php

namespace Zubi\ArticleBundle\Form\Person; 


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class PersonForm extends AbstractType {
   public function buildForm(FormBuilder $builder, array $options) {      
          $builder -> add('imie', 'text' );          
          $builder -> add('nazwisko', 'text' );
    }
    
    public function getName() {
        return 'PersonForm';
    }
}

