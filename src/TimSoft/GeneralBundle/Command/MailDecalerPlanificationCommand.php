<?php

namespace TimSoft\GeneralBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MailDecalerPlanificationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('mail:DecalerPlanification')
            ->setDescription('...')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $users = $em->getRepository('TimSoftGeneralBundle:Utilisateur')->findByRoleUtilisateur('ROLE_CHEF');
        $plannings = $em->getRepository('TimSoftGeneralBundle:Planning')->findAll();
        $today = new \DateTime();
        foreach ($plannings as $planning) {
            if ($planning->getStart() < $today && $planning->getStatut() != 'Terminé') {
                $user = $planning->getUtilisateur();
//                $output->writeln($today->diff($planning->getStart())->format("%a") . ' ' . $planning->getStart()->format('c'));
                if ($today->diff($planning->getStart())->format("%a") == 1 || $today->diff($planning->getStart())->format("%a") == 3 || $today->diff($planning->getStart())->format("%a") == 5 && $planning->getAlert() <= 3) {
                    $output->writeln($today->diff($planning->getStart())->format("%a") . ' ' . $planning->getStart()->format('c'));
                    $message = (new \Swift_Message('Rappel de Mettre à jour la prestation du ' . $planning->getStart()->format('d/m/Y')))
                        ->setFrom(['timplan@timsoft.net' => "Administrateur TimSoft"])
                        ->setBody('Bonjour ' . $user->getPrenomUtilisateur() . ',

<p>Ceci est un rappel pour mettre à jour (décaler ou annuler) la prestation du ' . $planning->getStart()->format('d/m/Y') . ' sur <a href="timplan.timsoft.net"> Timplan</a>. </p>

<p>Merci de prendre quelques minutes pour le faire,</p>


<p>En cas de soucis, veuillez-contacter L\'administrateur Timplan sur l\'adresse Timplan@timsoft.net</p>


<p>Cordialement,</p>

            ', 'text/html');
                    $failedRecipients = [];
                    $numSent = 0;
                    $message->addTo($user->getEmail(), $user->getPrenomUtilisateur() . ' ' . $user->getNomUtilisateur());
                    $message->addCc('w.benmustapha@timsoft.com.tn', 'Wafa Benmustapha');
                    $message->addCc('f.cherif@timsoft.com.tn', 'Fatma CHERIF');
                    if ($today->diff($planning->getStart())->format("%a") == 5) {
                        $output->writeln('5 jours');
                        $chefBU = $planning->getLc()->getCommande()->getBuManager();
                        if ($chefBU) {
                            $message->addTo($chefBU->getEmail(), $chefBU->getPrenomUtilisateur() . ' ' . $chefBU->getNomUtilisateur());
                        }
                        $message->addCc('c.navarro@timsoft.com.tn', 'Carlos NAVARRO');
                    }

                    $numSent += $this->getContainer()->get('mailer')->send($message, $failedRecipients);

                    $planning->setAlert($planning->getAlert() + 1);
                    $em->persist($planning);
                    $em->flush();

                }
            }
        }
    }
}
