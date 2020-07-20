<?php

namespace TimSoft\GeneralBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientTimSoftType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('codeClient')
                ->add('raisonSociale')
                ->add('clientsFacture')
                ->add('paysClient')
                ->add('adresseClient')
                ->add('villeClient')
                ->add('codePostalClient')
                ->add('telephone')
                ->add('faxClient')
                ->add('adresseMailClient')
                ->add('dateAdhesionClient');
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TimSoft\GeneralBundle\Entity\ClientTimSoft'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'timsoft_generalbundle_clienttimsoft';
    }


}
