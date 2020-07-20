<?php

namespace TimSoft\GeneralBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MailNonPlanificationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('mail:NonPlanification')
            ->setDescription('...')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $utilisateurs = $em->getRepository('TimSoftGeneralBundle:Utilisateur')->findByRoleUtilisateur('ROLE_CONSULTANT');
        foreach ($utilisateurs as $utilisateur) {
            $plannings = $em->getRepository('TimSoftGeneralBundle:Planning')->planning15Jours($utilisateur);
            if (count($plannings) == 0 && $utilisateur->isEnabled()) {
                $output->writeln($utilisateur->__toString());
                $output->writeln('andouch');
                $message = (new \Swift_Message('Rappel de planification'))
                    ->setFrom(['timplan@timsoft.net' => "Administrateur TimSoft"])
                    ->setBody('Bonjour ' . $utilisateur->getPrenomUtilisateur() . ',

<p>Votre planning était vide durant ces deux dernières semaines, il est temps de contacter vos clients et générer des prestations.</p>

<p>Cordialement,</p>

            ', 'text/html');
                $failedRecipients = [];
                $numSent = 0;
                $message->addTo($utilisateur->getEmail(), $utilisateur->getPrenomUtilisateur() . ' ' . $utilisateur->getNomUtilisateur());
                $numSent += $this->getContainer()->get('mailer')->send($message, $failedRecipients);
            }
        }
        $output->writeln('Command result.');
    }

}
