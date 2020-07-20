<?php

namespace TimSoft\GeneralBundle\Form;

use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use Symfony\Component\Form\FormBuilderInterface;

class UtilisateurType extends BaseType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('prenomUtilisateur')
                ->add('telephoneUtilisateur')
                ->add('statutUtilisateur')
                ->add('situationProfilUtilisateur');
    }
    
    public function getName()
    {
        return 'timsoft_ajouter_un_utilisateur';
    }
  }
