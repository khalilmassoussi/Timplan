<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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


class ProfileFormType extends AbstractType
{


    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle'))
            ->add('nomUtilisateur')
            ->add('prenomUtilisateur')
            ->add('telephoneUtilisateur')
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
            ->add('client')
            ->add('bus', EntityType::class, [
                'class' => Bu::class,
                'required' => false,
                'choice_label' => 'libelle',
                'multiple' => true,

            ])
            ->add('statutProfil', ChoiceType::class, array(
                'required' => false,
                'expanded' => false,
                'multiple' => false,
                'placeholder' => 'Introduire le statut du profil',
                'choices' => array(
                    'Profil Activé' => '1',
                    'Profil Déactivé' => '0'
                )))
            ->add('photoDeProfilFile', FileType::class, array(
                'attr' => array(
                    'data-name' => 'Photo',
                    'class' => 'form-control',
                ),
                'required' => $options['obligatoire'],
                'data_class' => null))
            ->remove('roles');
    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }

    public function getBlockPrefix()
    {
        return 'app_bundle_profile_type';
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\ProfileFormType';
    }
}
