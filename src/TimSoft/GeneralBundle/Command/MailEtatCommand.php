<?php

namespace TimSoft\GeneralBundle\Command;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MailEtatCommand extends ContainerAwareCommand
{


//    public function __construct(ContainerInterface $container)
//    {
//        parent::__construct();
//        $this->templating = $container->get('templating');
//
//    }
    protected function configure()
    {
        $this
            ->setName('mail:Etat')
            ->setDescription('...')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $engine = $this->getContainer()->get('templating');
        $date = new \DateTime();
        setlocale(LC_TIME, 'fr');
        $interval = new \DateInterval('P1M');
        $date->add($interval);
        $mois = strftime("%B", strtotime($date->format('F')));
        $previous_week = strtotime("-1 week +1 day");

        $start_week = strtotime("monday midnight", $previous_week);
        $end_week = strtotime("next friday", $start_week);

        $start_week = date("Y-m-d", $start_week);
        $end_week = date("Y-m-d", $end_week);
        $bus = $em->getRepository('TimSoftBuBundle:Bu')->findAll();

        foreach ($bus as $bu) {
            if ($bu->getChef()) {
                $output->writeln('-------------');
                $output->writeln($bu->getLibelle());
                $array = [];
                $listClient = [];
                foreach ($bu->getUtilisateurs() as $item) {
//                    $output->writeln($item->getNomUtilisateur());
                    $Proposé = $em->getRepository('TimSoftGeneralBundle:Planning')->countByStatut($item, 'Proposé', $date);
                    $Confirmé = $em->getRepository('TimSoftGeneralBundle:Planning')->countByStatut($item, 'Confirmé', $date);
                    $EnAttente = $em->getRepository('TimSoftGeneralBundle:Planning')->countByStatut($item, 'En Attente', $date);
                    $Terminé = $em->getRepository('TimSoftGeneralBundle:Planning')->countByStatut($item, 'Terminé', $date);
                    $Rejeté = $em->getRepository('TimSoftGeneralBundle:Planning')->countByStatut($item, 'Rejeté', $date);
                    $array[] = array('utilisateur' => $item, 'propose' => $Proposé, 'confirme' => $Confirmé, 'enattente' => $EnAttente, 'termine' => $Terminé, "rejete" => $Rejeté);
                }
                $clients = $em->getRepository('TimSoftCommandeBundle:LigneCommande')->getClientByBu($bu);
                foreach ($clients as $client) {
                    $totrap = 0;
                    $totral = 0;
                    $valtotrap = 0;
                    $valtotral = 0;
                    if ($client != null) {
                        $clientt = $em->getRepository('TimSoftGeneralBundle:Client')->findOneById($client);
                        $output->writeln($clientt->getRaisonSociale());
                        $lcs = $em->getRepository('TimSoftCommandeBundle:LigneCommande')->getbyBu($client, $start_week, $end_week);
                        foreach ($lcs as $lc) {
                            $totrap = $totrap + $lc->JRestant();
                            $valtotrap = $valtotrap + ($lc->JRestant() * $lc->calcul());
                            $totral = $totral + $lc->getQteRestante();
                            $valtotral = $valtotral + ($lc->getQteRestante() * $lc->calcul());
                            //                        $output->writeln($lc->getCommande()->getClient()->getRaisonSociale());
//                            $output->writeln($lc->getCommande()->getNCommande());
//                            $output->writeln($lc->JRestant());
//                            $output->writeln($lc->calcul());
                        }
                    }
                    $listClient[] = array('Client' => $clientt, 'RAP' => $totrap, 'ValRAP' => $valtotrap, 'RAL' => $totral, 'ValRAL' => $valtotral);
                }
//                $output->writeln(json_encode($listClient));
                $message = (new \Swift_Message('Récap BU ' . $bu->getLibelle() . ' du mois ' . $mois))
                    ->setFrom(['timplan@timsoft.net' => "Administrateur TimSoft"])
                    ->setBody($engine->render(
                    // app/Resources/views/Emails/registration.html.twig
                        '@TimSoftGeneral/General/etat.html.twig',
                        ['array' => $array, 'client' => $listClient, 'chef' => $bu->getChef()]
                    ),
                        'text/html');
                $failedRecipients = [];
                $numSent = 0;
                $message->addTo($bu->getChef()->getEmail(), $bu->getChef()->getPrenomUtilisateur() . ' ' . $bu->getChef()->getNomUtilisateur());
                $message->addCc('c.navarro@timsoft.com.tn', 'Carlos NAVARRO');
                $message->addCc('ttrabelsi@timsoft.com.tn', 'Tarek TRABELSI');

                $numSent += $this->getContainer()->get('mailer')->send($message, $failedRecipients);
            }
        }
    }
}
