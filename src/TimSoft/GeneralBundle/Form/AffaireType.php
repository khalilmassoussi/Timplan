<?php

namespace TimSoft\GeneralBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AffaireType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('libelle', TextType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('client', EntityType::class, [
                'class' => 'TimSoft\GeneralBundle\Entity\Client',
                'choice_label' => function ($client) {
                    return $client->getRaisonSociale() . ' - ' . $client->getCodeClient();
                },
                'placeholder' => 'Choisir un client',
                'attr' => ['class' => 'form-control']
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TimSoft\GeneralBundle\Entity\Affaire'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'timsoft_generalbundle_affaire';
    }


}
