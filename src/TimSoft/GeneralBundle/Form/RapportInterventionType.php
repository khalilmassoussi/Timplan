<?php

namespace TimSoft\GeneralBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class RapportInterventionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('compteRenduIntervention', TextareaType::class, array(
                'required' => true,
            ))
            ->add('remarqueClient')
            ->add('confirmationDeInterventionParClient', CheckboxType::class, array(
                'required' => false,
            ))
            ->add('fichiersJoin', CollectionType::class, array(
                'entry_type' => FichierJoinType::class,
                'prototype' => true,
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true
            ))
            ->add('FeuilleDePresence', EntityType::class, array(
                'class' => 'TimSoftGeneralBundle:FeuilleDePresence',
//                'attr' => ['readonly' => true],
                'disabled' => true,
                'query_builder' => function (\TimSoft\GeneralBundle\Repository\FeuilleDePresenceRepository $er) use ($builder) {
                    if ($builder->getData()->getId() == null) {
                        return $er->getFeuilleSansRapport();
                    } else {
                        $er->findAll();
                    }

                }, 'placeholder' => 'Sélectionnez les informations sur l\'intervention', 'attr' => array('class' => 'IDFeuille form-control')));
        //->add('FeuilleDePresence',null,array('placeholder' => 'Sélectionnez les informations sur l\'intervention','attr'=> array('class' => 'IDFeuille')));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TimSoft\GeneralBundle\Entity\RapportIntervention'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'timsoft_generalbundle_rapportintervention';
    }


}
