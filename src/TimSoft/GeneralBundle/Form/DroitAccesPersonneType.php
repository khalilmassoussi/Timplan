<?php

namespace TimSoft\GeneralBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class DroitAccesPersonneType extends AbstractType
{
   
    /**
     * {@inheritdoc}
     */ 
     public function buildForm(FormBuilderInterface $builder, array $options)
    {       $builder 
                       ->add('autorisation', ChoiceType::class , array(
                       'required' => false,
                       'expanded' => false,
                       'multiple' => false,
                           'placeholder' => 'Choisir le type d\'action à faire',
                       'choices' => array(
                            'Ajouter un droit'  => '1' ,
                            'Retirer un droit' => '0' 
                       )));
                   
               
   
    }
    
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TimSoft\GeneralBundle\Entity\DroitAccesPersonne'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'timsoft_generalbundle_droitaccespersonne';
    }


}
