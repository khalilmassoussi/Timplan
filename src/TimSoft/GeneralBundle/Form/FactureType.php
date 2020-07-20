<?php

namespace TimSoft\GeneralBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;



class FactureType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('numeroFacture')
                ->add('dateFacture', DateType::class, array(
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd',
                ))
                ->add('natureFacture', ChoiceType::class , array(
                       'required' => true,
                       'expanded' => false,
                       'multiple' => false,
                       'choices' => array(
                            'Facture'  => 'Facture' ,
                            'Avoir' => 'Avoir' 
                       ),
                    'preferred_choices' => 'Facture',
                   
               ))
                ->add('statutFacture', ChoiceType::class , array(
                       'required' => true,
                       'expanded' => false,
                       'multiple' => false,
                       'choices' => array(
                            'Non traitée'  => 'Non traitée' ,
                            'Validée' => 'Validée',
                            'Règlement en cours' => 'Règlement en cours',
                            'Règlement prêt' => 'Règlement prêt',
                            'Refusée' => 'Refusée',
                       ),
                       'preferred_choices' => 'Non traitée',
                        'attr' => array ('class' => 'StatutFacture form-control')
                   )
               )
                ->add('montantTTCFacture')
                        //, MoneyType::class)
                 //array('currency' => 'TND',))
//                ->add('facturePDF', FileType::class, 
//                        array('required' => $options['obligatoire'],'data_class' => null))
                ->add('factureFile', FileType::class, 
                       array('required' => $options['obligatoire']))
                ->add('client' , null, array(
                    'placeholder' => 'Sélectionnez le client',
                    'required' =>  true,
                   
                ))
                ->add('causeRefusFacture'); 
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TimSoft\GeneralBundle\Entity\Facture',
            'obligatoire' => null,
            'attr' => ['id' => 'EditForm']
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'timsoft_generalbundle_facture';
    }


}
