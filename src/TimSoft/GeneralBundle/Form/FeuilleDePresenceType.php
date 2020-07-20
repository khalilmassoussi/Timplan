<?php

namespace TimSoft\GeneralBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class FeuilleDePresenceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('numeroCommande', TextType::class, array('attr' => ['readonly' => true]))
            ->add('libelleCommande')
            ->add('modeIntervention', ChoiceType::class, array(
                'placeholder' => 'Choisir le mode d\'intervention',
                'required' => false,
                'expanded' => false,
                'multiple' => false,
                'choices' => array(
                    'Sur site' => 'Sur Site',
                    'à distance' => 'à distance',
                )
            ))
            ->add('dateIntervention', DateType::class, array(
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'attr' => ['readonly' => true]
            ))
            ->add('heureDebutInterventionMatin', TimeType::class, array(
                'required' => false,
//                'hours' => ["08", "09", "10", "11", "12", "13", "14"],
//                'placeholder' => array(
//                    'hour' => 'Heure', 'minute' => 'Minute',
//                ),
                'widget' => 'single_text',
                'attr' => array('class' => 'form-control '),
            ))
            ->add('heureFinInterventionMatin', TimeType::class, array(
                'required' => false,
//                "hours" => ["08", "09", "10", "11", "12", "13", "14"],
//                'placeholder' => array(
//                    'hour' => 'Heure', 'minute' => 'Minute',
//                ),
                'widget' => 'single_text',
                'attr' => array('class' => 'form-control '),
            ))
            ->add('heureDebutInterventionAM', TimeType::class, array(
                'required' => false,
//                "hours" => ["15", "16", "17", "18", "19", "20"],
//                'placeholder' => array(
//                    'hour' => 'Heure', 'minute' => 'Minute',
//                ),
                'widget' => 'single_text',
                'attr' => array('class' => 'form-control '),
            ))
            ->add('heureFinInterventionAM', TimeType::class, array(
                'required' => false,
//                "hours" => ["15", "16", "17", "18", "19", "20"],
//                'placeholder' => array('hour' => 'Heure', 'minute' => 'Minute'),
                'widget' => 'single_text',
                'attr' => array('class' => 'form-control '),
            ))
            ->add('statutValidation', CheckboxType::class, array(
                'required' => false,
                'label' => 'Validée',
                'attr' => array('class' => 'form-control toggle__input'),
            ))
            ->add('validationClient', ChoiceType::class, array(
                'required' => true,
                'label' => 'Validation client',
                'expanded' => true,
//                'attr' => array('class' => 'custom-control-input'),
                'choices' => [
                    'Obligatoire ' => 1,
                    'Non obligatoire ' => 0,
                ],
                'choice_attr' => function ($choice, $key, $value) {

                    return ['style' => 'margin-right: 10px; margin-left:10px'];
                },
            ))
            ->add('intervenant')
            ->add('client', null, array('placeholder' => 'Sélectionnez le Client', 'required' => true, 'attr' => array('class' => 'Client form-control')))
            //->add('participants', ReferentType::class)
            ->add('participants', CollectionType::class, array(
                'required' => false,
                'entry_type' => ReferentType::class,
                'prototype' => true,
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true
            ));

        // $builder->get('client')->add(\FOS\UserBundle\Event\FormEvent::PRE_SET_DATA , function (\FOS\UserBundle\Event\FormEvent $event){}, $options)
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TimSoft\GeneralBundle\Entity\FeuilleDePresence'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'timsoft_generalbundle_feuilledepresence';
    }


}
