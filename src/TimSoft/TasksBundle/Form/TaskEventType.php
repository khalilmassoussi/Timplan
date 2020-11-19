<?php

namespace TimSoft\TasksBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use TimSoft\GeneralBundle\Repository\UtilisateurRepository;
use TimSoft\TasksBundle\Entity\Activite;

class TaskEventType extends AbstractType
{
    private $em;


    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('site', ChoiceType::class, [
            'choices' => [
                'Télétravail' => 'Télétravail',
                'Bureau' => 'Bureau',
                'Extérieur' => 'Extérieur',
            ],
            'placeholder' => 'Choisir le site',
        ])
            ->add('etiquette', ChoiceType::class, [
                'choices' => [
                    'Important' => 'Important',
                    'Urgent' => 'Urgent',
                    'Moyen' => 'Moyen',
                    'Peu' => 'Peu'
                ],
                'placeholder' => "Choisir l'étiquette",
            ])
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'To DO' => 'To DO',
                    'In Progress' => 'In Progress',
                    'Done' => 'Done',
                    'Bloqué' => 'Bloqué'
                ],
                'placeholder' => 'Choisir le statut',
//                'empty_data' => 'Choisir'
            ])
            ->add('progression', ChoiceType::class, [
                'choices' => [
                    '0%' => 0,
                    '25%' => 25,
                    '50%' => 50,
                    '75%' => 75,
                    '90%' => 90,
                    '100%' => 100,
                ],
                'expanded' => true,
            ])
            ->add('start', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['placeholder' => 'Choisir la date/heure de debut', 'readonly' => 'readonly', 'style' => 'background:white;cursor:pointer'],
                'format' => 'dd MMMM yyyy HH:mm',
                'label' => 'Debut'
//                'placeholder' => 'Choisir la date/heure de debut'
            ])
            ->add('end', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['placeholder' => 'Choisir la date/heure de fin', 'readonly' => 'readonly', 'style' => 'background:white;cursor:pointer'],
                'format' => 'dd MMMM yyyy HH:mm',
                'label' => 'Fin'
            ])
            ->add('utilisateur', EntityType::class, [
                    'class' => 'TimSoft\GeneralBundle\Entity\Utilisateur',
                    'placeholder' => 'Choisir un utilisateur',
                    'query_builder' => function (UtilisateurRepository $er) {
                        return $er->getByEnabled();
                    }
                ]
            )
            ->add('client', EntityType::class, [
                'class' => 'TimSoft\GeneralBundle\Entity\Client',
                'choice_label' => 'raisonSociale',
                'placeholder' => 'Choose an option',
                'required' => true,
            ])
            ->add('rapport', TextareaType::class, [
                'attr' => ['placeholder' => 'Rédiger le rapport de la task'],
            ])
            ->add('activite', EntityType::class, [
                'class' => Activite::class,
                'required' => false,
                'placeholder' => 'Choisir une activité',
            ])
//
            ->add('allDay', ChoiceType::class, [
                'choices' => [
                    'Toute la journée' => true,
                    'Temps partiel' => false,
//                    'Periodique' => 'Periodique',
                ],
                'expanded' => true,
                'label' => 'Type',
                'block_name' => 'allDayForm',
                'label_attr' => ['class' => 'checkbox-custom'],
            ])
            ->add('motif', TextType::class, array(
                'attr' => ['placeholder' => 'Motif de blocage'],
                'required' => false,
            ))
            ->add('periodique', ChoiceType::class, [
                'choices' => [
                    'Periodique' => true,
                    'Non Periodique' => false,
//                    'Periodique' => 'Periodique',
                ],
                'empty_data' => false,
                'expanded' => true,
                'label' => 'Periodique',
                'block_name' => 'PeriodiqueForm',
                'label_attr' => ['class' => 'checkbox-custom'],
            ])
            ->add('freq', ChoiceType::class, [
                'choices' => [
                    'Hebdomadaire' => 'weekly',
                    'Mensuel' => 'monthly',
                ],
                'expanded' => false,
                'label' => 'Périodicité',
                'block_name' => 'PeriodiqueForm',
                'attr' => ['class' => 'form-control']
//                'label_attr' => ['class' => 'checkbox-custom'],
            ])
            ->add('intervale', IntegerType::class, [
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'col-sm-3 col-form-label'],
            ])
            ->add('byweekday', ChoiceType::class, [
                'choices' => [
                    'Lu' => 'mo',
                    'Ma' => 'tu',
                    'Me' => 'we',
                    'Je' => 'th',
                    'Ve' => 'fr',
                    'Sa' => 'sa',
                    'Di' => 'su',
                ],
                'expanded' => true,
                'multiple' => true,
                'label' => 'Jours de la semaine',
                'block_name' => 'PeriodiqueForm',
                'attr' => ['class' => 'form-control']
//                'label_attr' => ['class' => 'checkbox-custom'],
            ]);
        $formModifier = function (FormInterface $form, Activite $activite = null) {
            $tasks = null === $activite ? [] : $activite->getTasks();

            $form->add('task', EntityType::class, [
                'class' => 'TimSoft\TasksBundle\Entity\Task',
                'placeholder' => 'Choisir Task',
                'choices' => $tasks,
            ]);
        };
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                $form = $event->getForm();

                // this would be your entity, i.e. SportMeetup
                $data = $event->getData();

                $activite = $form->get('activite');

                $formModifier($event->getForm(), $data->getActivite());

                if ($data->isAllDay()) {
                    $form->add('start', DateType::class, [
                        'widget' => 'single_text',
                        'html5' => false,
                        'attr' => ['placeholder' => 'Choisir la date/heure de debut'],
                        'format' => 'dd MMMM y',
                        'label' => 'Debut'
//                'placeholder' => 'Choisir la date/heure de debut'
                    ]);
                    $form->add('end', DateType::class, [
                        'widget' => 'single_text',
                        'html5' => false,
                        'attr' => ['placeholder' => 'Choisir la date/heure de fin'],
                        'format' => 'dd MMMM y',
                        'label' => 'Fin'
                    ]);
                }
            }
        );

        $builder->get('activite')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                // It's important here to fetch $event->getForm()->getData(), as
                // $event->getData() will get you the client data (that is, the ID)
                $activite = $event->getForm()->getData();

                // since we've added the listener to the child, we'll have to pass on
                // the parent to the callback functions!
                $formModifier($event->getForm()->getParent(), $activite);
            }
        );
        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();

                if (array_key_exists('allDay', $data) && $data['allDay'] == 1) {
                    $form->add('start', DateType::class, [
                        'widget' => 'single_text',
                        'html5' => false,
                        'attr' => ['placeholder' => 'Choisir la date/heure de debut'],
                        'format' => 'dd MMMM y',
                        'label' => 'Debut'
//                'placeholder' => 'Choisir la date/heure de debut'
                    ]);
                    $form->add('end', DateType::class, [
                        'widget' => 'single_text',
                        'html5' => false,
                        'attr' => ['placeholder' => 'Choisir la date/heure de fin'],
                        'format' => 'dd MMMM y',
                        'label' => 'Fin'
                    ]);
                } else {
                    $form->add('start', DateType::class, [
                        'widget' => 'single_text',
                        'html5' => false,
                        'attr' => ['placeholder' => 'Choisir la date/heure de debut'],
                        'format' => 'dd MMMM y HH:mm',
                        'label' => 'Debut'
//                'placeholder' => 'Choisir la date/heure de debut'
                    ]);
                    $form->add('end', DateType::class, [
                        'widget' => 'single_text',
                        'html5' => false,
                        'attr' => ['placeholder' => 'Choisir la date/heure de fin'],
                        'format' => 'dd MMMM y HH:mm',
                        'label' => 'Fin'
                    ]);
                }
            });
    }

    /**
     * {@inheritdoc}
     */
    public
    function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TimSoft\TasksBundle\Entity\TaskEvent'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public
    function getBlockPrefix()
    {
        return 'timsoft_tasksbundle_taskevent';
    }


}
