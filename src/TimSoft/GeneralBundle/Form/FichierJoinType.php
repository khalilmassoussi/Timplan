<?php

namespace TimSoft\GeneralBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class FichierJoinType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('nomFichierJoin', TextType::class, array(
                    'attr' => array(
                            'data-name' => 'nom',
                            'class' => 'form-control',
                        )
                ))
                ->add('fichierFile', FileType::class, array(
                    'attr' => array(
                            'data-name' => 'fichier',
                            'class' => 'form-control',
                        ),
                    'required' => $options['obligatoire'],
                    'data_class' => null))
                ->add('sendParMail', CheckboxType::class, array(
                    'attr' => array(
                            'data-name' => 'envoyer',
                        ),
                    'required' => false
                ))
                ->remove('RapportIntervention');
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TimSoft\GeneralBundle\Entity\FichierJoin',
            'obligatoire' => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'timsoft_generalbundle_fichierjoin';
    }


}
