<?php

namespace TimSoft\GeneralBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlanningType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('start', DateType::class, [
            'widget' => 'single_text',
            'html5' => false,
            'attr' => ['placeholder' => 'Choisir la date/heure de debut'],
            'format' => 'dd MMMM yyyy',
            'label' => 'Debut'
//                'placeholder' => 'Choisir la date/heure de debut'
        ])
            ->add('end', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['placeholder' => 'Choisir la date/heure de fin', 'readonly' => true,],
                'format' => 'dd MMMM yyyy',
                'label' => 'Fin',

//                'disabled' => true
            ])
            ->add('temps', ChoiceType::class, [
                'choices' => [
                    'Matin' => 'Matin',
                    'Après-midi' => 'Après-midi',
                ],
                'multiple' => true,
                'required' => true,
                'mapped' => false,
                'expanded' => true
            ])
            ->add('statut', ChoiceType::class, [
                    'choices' => [
                        'Confirmé' => 'Confirmé',
                        'Proposé' => 'Proposé',
                        'Rejeté' => 'Rejeté',
                        'En attente' => 'En attente',
                        'Terminé' => 'Terminé'
                    ],
                    'choice_attr' => function ($key, $val, $index) {
                        $disabled = false;
                        if ($val == 'Terminé') {
                            $disabled = true;
                        }
                        // set disabled to true based on the value, key or index of the choice...
                        return $disabled ? ['disabled' => 'disabled'] : [];
                    },
                ]
            )
//            ->add('jSupplementaire', RadioType::class)
            ->add('facturation', ChoiceType::class, [
                'choices' => [
                    'Facturable' => 'Facturé',
                    'Gratuit' => 'Gratuit',
                ],
                'expanded' => true,
                'label' => 'Facturation',
                'block_name' => 'lieuForm',
                'label_attr' => ['class' => 'checkbox-custom'],])
            ->add('lieu', ChoiceType::class, [
                'choices' => [
                    'Sur site' => 'Sur site',
                    'A distance' => 'à distance',
                ],
                'expanded' => true,
                'label' => 'Lieu',
                'block_name' => 'lieuForm',
                'label_attr' => ['class' => 'checkbox-custom'],])
            ->add('commentaire', TextareaType::class, ['required' => false])
            ->add('utilisateur')
            ->add('accompagnements')
            ->add('confirmePar');

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $planning = $event->getData();
            $form = $event->getForm();
            if ($planning && $planning->isAllDay()) {
                $form->add('temps', ChoiceType::class, [
                    'choices' => [
                        'Matin' => 'Matin',
                        'Après-midi' => 'Après-midi',
                    ],
                    'data' => ['Matin', 'Après-midi'],
//                    'data' => 'Matin',
                    'multiple' => true,
                    'mapped' => false,
                    'expanded' => true
                ]);
            } else {
                if ($planning->getStart()->format('H') <= 13) {
                    $form->add('temps', ChoiceType::class, [
                        'choices' => [
                            'Matin' => 'Matin',
                            'Après-midi' => 'Après-midi',
                        ],
                        'data' => ['Matin'],
//                    'data' => 'Matin',
                        'multiple' => true,
                        'mapped' => false,
                        'expanded' => true
                    ]);
                } else {
                    $form->add('temps', ChoiceType::class, [
                        'choices' => [
                            'Matin' => 'Matin',
                            'Après-midi' => 'Après-midi',
                        ],
                        'data' => ['Après-midi'],
                        'multiple' => true,
                        'mapped' => false,
                        'expanded' => true
                    ]);
                }
            }
        });

//        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
//            $planning = $event->getData();
//            $form = $event->getForm();
//            $temps = $form->get('temps')->getData();
//            if (in_array('Matin', $temps) && in_array('Après-midi', $temps)) {
//                $planning->setAllDay(true);
//            } elseif (in_array('Matin', $temps) && !in_array('Après-midi', $temps)) {
//                $planning->setAllDay(false);
//                $planning->setStart($planning->getStart()->setTime(14, 55));
//            }
//        });
    }


    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TimSoft\GeneralBundle\Entity\Planning'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'timsoft_generalbundle_planning';
    }


}
