<?php

namespace TimSoft\GeneralBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ReferentType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nomParticipant', TextType::class, array(
            'attr' => array(
                'data-name' => 'nom',//---give this attribute any name you want
                'class' => 'form-control',
            ),
            'required' => true,
        ))
            ->add('prenomParticipant', TextType::class, array(
                'attr' => array(
                    'data-name' => 'prenom',//---give this attribute any name you want
                    'class' => 'form-control',
                ),
                'required' => true,
            ))
            ->add('adresseMailParticipant', TextType::class, array(
                'attr' => array(
                    'data-name' => 'mail',//---give this attribute any name you want
                    'class' => 'form-control',
                ),
                'required' => true,
            ))
            ->remove('societe')
            ->remove('intervention');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TimSoft\GeneralBundle\Entity\Referent'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'timsoft_generalbundle_referent';
    }


}
