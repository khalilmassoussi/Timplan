<?php

namespace TimSoft\GeneralBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class DroitAccesGroupeType extends AbstractType
{
   
    /**
     * {@inheritdoc}
     */ 
     public function buildForm(FormBuilderInterface $builder, array $options)
    {       $builder 
                       ->add('autorisationCategorie', ChoiceType::class , array(
                       'mapped' => false,
                       'required' => false,
                       'expanded' => false,
                       'multiple' => false,
                           'placeholder' => 'Choisir le type d\'action à faire',
                       'choices' => array(
                            'Ajouter une catégorie'  => '1' ,
                            'Retirer une catégorie' => '0' 
                       )));
                   
               
   
    }
    
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TimSoft\GeneralBundle\Entity\DroitAccesGroupe'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'timsoft_generalbundle_droitaccesgroupe';
    }


}
