<?php

namespace TimSoft\GeneralBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MailRappelFPCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('mail:RappelFP')
            ->setDescription('...')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $users = $em->getRepository('TimSoftGeneralBundle:Utilisateur')->findByRoleUtilisateur('ROLE_CHEF');
        $fps = $em->getRepository('TimSoftGeneralBundle:Planning')->findAll();
        $today = new \DateTime();
        foreach ($fps as $planning) {
            if ( $planning->getStatut() == 'Terminé'&& !$planning->getFeuille()->getStatutValidation()) {
                $user = $planning->getUtilisateur();
//                $output->writeln($today->diff($planning->getStart())->format("%a") . ' ' . $planning->getStart()->format('c'));
                if ($today->diff($planning->getStart())->format("%a") == 1 || $today->diff($planning->getStart())->format("%a") == 3 || $today->diff($planning->getStart())->format("%a") == 5 && $planning->getAlert() <= 3) {
                    $output->writeln($today->diff($planning->getStart())->format("%a") . ' ' . $planning->getStart()->format('c'));
                    $message = (new \Swift_Message('Rappel de Mettre à jour feuille de presence ' . $planning->getLc()->getCommande()->getNCommande()))
                        ->setFrom(['Timplan@timsoft-solutions.com' => "Administrateur Timplan"])
                        ->setBody('Bonjour ' . $user->getPrenomUtilisateur() . ',

<p>Rappel : Prière de procéder à la rédaction->validation de la feuille de présence de la journée du ' . $planning->getStart()->format('d/m/Y') . ' sur <a href="timplan.timsoft.net"> Timplan</a>. </p>

<p>Merci de prendre quelques minutes pour le faire,</p>


<p>En cas de soucis, veuillez-contacter L\'administrateur Timplan sur l\'adresse Timplan@timsoft-solutions.com</p>


<p>Cordialement,</p>

            ', 'text/html');
                    $failedRecipients = [];
                    $numSent = 0;
                    $message->addTo($user->getEmail(), $user->getPrenomUtilisateur() . ' ' . $user->getNomUtilisateur());
                    if ($today->diff($planning->getStart())->format("%a") == 5) {
                        $bu = $planning->getLc()->getBu();
                        $bu = $em->getRepository('TimSoftBuBundle:Bu')->findOneByLibelle($bu);
                        $chefBU = $bu->getChef();
                        $message->addTo($chefBU->getEmail(), $chefBU->getPrenomUtilisateur() . ' ' . $chefBU->getNomUtilisateur());

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
