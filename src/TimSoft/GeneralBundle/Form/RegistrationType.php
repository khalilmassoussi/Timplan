<?php
/**
 * Created by PhpStorm.
 * User: Walid
 * Date: 03/11/2020
 * Time: 16:58
 */

namespace TimSoft\GeneralBundle\Form;


use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use TimSoft\BuBundle\Entity\Bu;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle'))
            ->add('nomUtilisateur')
            ->add('prenomUtilisateur')
            ->add('telephoneUtilisateur', null, array('required' => false))
            ->add('roleUtilisateur', ChoiceType::class, array(
                    'placeholder' => 'Sélectionner le rôle',
                    'required' => true,
                    'expanded' => false,
                    'multiple' => false,
                    'choices' => array(
                        'Administrateur' => 'ROLE_ADMIN',
                        'Client' => 'ROLE_CLIENT',
                        'Consultant' => 'ROLE_CONSULTANT',
                        'Gestionnaire' => 'ROLE_GESTIONNAIRE',
                        'BU Manager' => 'ROLE_CHEF',
                        'Tracking User' => 'ROLE_TRACKING',
                        'Support' => 'ROLE_SUPPORT',
                        'Consultant externe' => 'ROLE_EXTERNE'
                    )
                )
            )
            ->add('client', null, array(
                'placeholder' => 'Choisir la société de de l\'utilisateur'))
            ->add('bus', EntityType::class, [
                'class' => Bu::class,
                'required' => false,
                'choice_label' => 'libelle',
                'multiple' => true,
            ])
            ->remove('username')
            ->remove('password')
            ->add('photoDeProfilFile', FileType::class, array(
                'attr' => array(
                    'data-name' => 'Photo',
                    'class' => 'form-control',
                ),
                'required' => false,
                'data_class' => null))
            ->remove('plainPassword');
    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }

    public function getBlockPrefix()
    {
        return 'app_bundle_registration_type';
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';
    }
}
