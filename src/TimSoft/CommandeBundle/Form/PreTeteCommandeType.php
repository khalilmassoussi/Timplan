<?php

namespace TimSoft\CommandeBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use TimSoft\GeneralBundle\Entity\Client;

class PreTeteCommandeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nCommande')
            ->add('client')
            ->add('datePiece', DateType::class, array(
                'widget' => 'single_text',
                'attr' => ['class' => 'js-datepicker'],
                'html5' => false,
            ))
            ->add('buManager');

           $formModifier = function (FormInterface $form, Client $client = null) {
               $affaire = null === $client ? [] : $client->getAffaires();

               $form->add('affaire', EntityType::class, [
                   'class' => 'TimSoft\GeneralBundle\Entity\Affaire',
                   'choice_label' => 'libelle',
                   'placeholder' => 'Choisir un affaire',
                   'choices' => $affaire,
                   'required' => false
               ]);
           };
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                // this would be your entity, i.e. SportMeetup
                $data = $event->getData();

                $formModifier($event->getForm(), $data->getClient());
            }
        );

        $builder->get('client')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                // It's important here to fetch $event->getForm()->getData(), as
                // $event->getData() will get you the client data (that is, the ID)
                $sport = $event->getForm()->getData();

                // since we've added the listener to the child, we'll have to pass on
                // the parent to the callback functions!
                $formModifier($event->getForm()->getParent(), $sport);
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TimSoft\CommandeBundle\Entity\PreTeteCommande'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'timsoft_commandebundle_pretetecommande';
    }


}
