<?php

namespace TimSoft\GeneralBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MailRappelPlanificationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('mail:rappelPlanification')
            ->setDescription('...')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $users = $em->getRepository('TimSoftGeneralBundle:Utilisateur')->findByRoleUtilisateur('ROLE_CHEF');


        $date = new \DateTime();
        setlocale(LC_TIME, 'fr');
        $interval = new \DateInterval('P1M');
        $date->add($interval);
        $mois = strftime("%B", strtotime($date->format('F')));
        foreach ($users as $user) {
            $output->writeln($user->getId());
            echo 'Rappel de Planifier le mois ' . strftime("%B", strtotime($date->format('F')));
            $message = (new \Swift_Message('Rappel de Planifier le mois ' . strftime("%B", strtotime($date->format('F')))))
                ->setFrom(['Timplan@timsoft-solutions.com' => "Administrateur Timplan"])
                ->setBody('Bonjour ' . $user->getPrenomUtilisateur() . ',

<p>Ceci est un rappel pour planifier les journées de vos consultants pour le mois <b>' . $mois . '</b> sur <a href="www.timplan.timsoft.net">Timplan</a>.</p>


<p>Merci de prendre quelques minutes pour le faire,</p>


<p>En cas de soucis, veuillez-contacter L\'administrateur Timplan sur l\'adresse Timplan@timsoft-solutions.com</p>


<p>Cordialement,</p>

            ', 'text/html');
            $failedRecipients = [];
            $numSent = 0;
            $message->addTo($user->getEmail(), $user->getPrenomUtilisateur() . ' ' . $user->getNomUtilisateur());

            $numSent += $this->getContainer()->get('mailer')->send($message, $failedRecipients);
        }
    }

}
