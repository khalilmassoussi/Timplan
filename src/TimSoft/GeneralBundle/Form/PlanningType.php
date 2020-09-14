<?php

namespace TimSoft\GeneralBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use TimSoft\GeneralBundle\Repository\UtilisateurRepository;

class PlanningType extends AbstractType
{

    public $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

//        $er = $options['foo_repository'];
        $builder
            ->add('start', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['placeholder' => 'Choisir la date/heure de debut', 'readonly' => 'readonly', 'style' => 'background:white;cursor:pointer'],
                'format' => 'dd MMMM yyyy',
                'label' => 'Debut'
            ])
            ->add('end', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['placeholder' => 'Choisir la date/heure de fin', 'readonly' => true,],
                'format' => 'dd MMMM yyyy',
                'label' => 'Fin',
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
                    if ($this->security->getUser()->getRoleUtilisateur() == 'ROLE_ADMIN') {
                        $disabled = false;
                    } elseif ($val == 'Terminé') {
                        $disabled = true;
                    }
                    // set disabled to true based on the value, key or index of the choice...
                    return $disabled ? ['disabled' => 'disabled'] : [];
                },
            ])
            ->add('facturation', ChoiceType::class, [
                'choices' => [
                    'Facturable' => 'Facturé',
                    'Gratuit' => 'Gratuit',
                ],
                'expanded' => true,
                'label' => 'Facturation',
                'block_name' => 'lieuForm',
                'label_attr' => ['class' => 'checkbox-custom'],
                'disabled' => true])
            ->add('lieu', ChoiceType::class, [
                'choices' => [
                    'Sur site' => 'Sur site',
                    'A distance' => 'à distance',
                ],
                'expanded' => true,
                'label' => 'Lieu',
                'block_name' => 'lieuForm',
                'label_attr' => ['class' => 'checkbox-custom',],
            ])
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
                    'multiple' => true,
                    'mapped' => false,
                    'expanded' => true
                ]);
            } else {
                if ($planning->getStart()->format('H') == 8) {
                    $form->add('temps', ChoiceType::class, [
                        'choices' => [
                            'Matin' => 'Matin',
                            'Après-midi' => 'Après-midi',
                        ],
                        'data' => ['Matin'],
//
                        'multiple' => true,
                        'mapped' => false,
                        'expanded' => true
                    ]);
                } elseif ($planning->getStart()->format('H') == 14) {
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
                } else {
                    $form->add('temps', ChoiceType::class, [
                        'choices' => [
                            'Matin' => 'Matin',
                            'Après-midi' => 'Après-midi',
                        ],
                        'data' => ['Matin', 'Après-midi'],
                        'multiple' => true,
                        'mapped' => false,
                        'expanded' => true
                    ]);
                }
            }

        });
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
//            $form->add('lc');
            $planning = $event->getData();
            $form->add('confirmePar', EntityType::class, [
                'class' => 'TimSoft\GeneralBundle\Entity\Utilisateur',
                'placeholder' => 'Choisir le chef de projet client',
                'required' => false,
                'query_builder' => function (UtilisateurRepository $repo) use ($planning) {
                    return $repo->findBySociete($planning->getLc()->getCommande()->getClient());
                }
            ]);
        });
    }


    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TimSoft\GeneralBundle\Entity\Planning'
//            'er' => null
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
