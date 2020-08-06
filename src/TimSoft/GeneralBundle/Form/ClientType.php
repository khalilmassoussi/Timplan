<?php

namespace TimSoft\GeneralBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('codeClient', null)
            ->add('raisonSociale', null, array(
                'required' => true,
            ))
            ->add('clientFacture')
            ->add('paysClient', CountryType::class, array(
                'preferred_choices' => array('TN'),
            ))
            ->add('adresseClient', null, array(
            ))
            ->add('villeClient', null, array(
            ))
            ->add('codePostalClient', null, array(
            ))
            ->add('telephone', TelType::class, array(
            ))
            ->add('faxClient', TelType::class, array(
            ))
            ->add('adresseMailClient', EmailType::class, array(
                'label' => 'form.email',
//                'translation_domain' => 'FOSUserBundle'
            ))
            ->add('dateAdhesionClient', DateType::class, array(
                'label' => 'Date d\'adhésion',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
            ))
            ->add('societeMere');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TimSoft\GeneralBundle\Entity\Client'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'timsoft_generalbundle_client';
    }


}
