<?php

namespace TimSoft\CommandeBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use TimSoft\GeneralBundle\Entity\Utilisateur;

class AffecterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
$builder
    ->add('array', HiddenType::class)
    ->add('users', EntityType::class, array(
        'class' => Utilisateur::class,
        'choice_label' => 'nomUtilisateur',
        'expanded' => true,
        'multiple' => false,
    ))
;

    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }

    public function getBlockPrefix()
    {
        return 'tim_soft_commande_bundle_affecter_type';
    }
}
