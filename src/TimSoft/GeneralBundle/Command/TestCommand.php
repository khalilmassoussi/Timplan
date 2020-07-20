<?php

namespace TimSoft\GeneralBundle\Command;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class TestCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:print-lines')
            // the short description shown while running "php bin/console list"
            ->setHelp("This command allows you to print some text in the console")
            // the full command description shown when running the command with
            ->setDescription('Prints some text into the console with given parameters.')
            // Set options
            ->setDefinition(
                new InputDefinition(array(
                    new InputOption('firstline', 'a', InputOption::VALUE_REQUIRED, "The first line to be printed", "Default First Line Value"),
                    new InputOption('secondline', 'b', InputOption::VALUE_OPTIONAL, "The second line to be printed", "Default First Line Value"),
                ))
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $previous_week = strtotime("-1 week +1 day");

        $start_week = strtotime("monday midnight", $previous_week);
        $end_week = strtotime("next friday", $start_week);

        $start_week = date("Y-m-d", $start_week);
        $end_week = date("Y-m-d", $end_week);
//        $output->writeln($start_week);
//        $output->writeln($end_week);
        $plannings = $em->getRepository('TimSoftGeneralBundle:Planning')->PlanningOfLastWeek();
        foreach ($plannings as $planning) {
            $output->writeln($planning->getStart()->format('Y-m-d'));
        }
        $message = (new \Swift_Message('Planning de la semaine'))
            ->setFrom(['timplan@timsoft.net' => "Administrateur TimSoft"])
            ->setBody('');
        $failedRecipients = [];
        $numSent = 0;
        $message->addTo('k.massoussi@timsoft.com.tn', 'khalil massoussi');
        $spreadsheet = new Spreadsheet();

        /* @var $sheet \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Worksheet */
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Client');
        $sheet->setCellValue('B1', 'Libelle');
        $sheet->setCellValue('C1', 'Date');
        $sheet->setCellValue('D1', 'N°Commande');
        $sheet->setCellValue('E1', 'Invtervenant');
        $sheet->setCellValue('F1', 'Lieu');
        $sheet->setCellValue('G1', 'Ressource D\'accompagnement');
        $sheet->setCellValue('H1', 'Facturation');
        $sheet->setCellValue('I1', 'Statut');
        $sheet->setCellValue('J1', 'Toute la journée');

        $counter = 2;
        foreach ($plannings as $planning) {
            $sheet->setCellValue('A' . $counter, $planning->getLc()->getCommande()->getClient()->getRaisonSociale());
            $sheet->setCellValue('B' . $counter, $planning->getLc()->getLibelle());
            $sheet->setCellValue('C' . $counter, $planning->getStart()->format('d-m-Y'));
            $sheet->setCellValue('D' . $counter, $planning->getLc()->getCommande()->getNCommande());
            $sheet->setCellValue('E' . $counter, $planning->getUtilisateur()->getPrenomUtilisateur() . ' ' . $planning->getUtilisateur()->getNomUtilisateur());
            $sheet->setCellValue('F' . $counter, $planning->getLieu());
            $sheet->setCellValue('G' . $counter, '');
            $sheet->setCellValue('H' . $counter, $planning->getFacturation());
            $sheet->setCellValue('I' . $counter, $planning->getStatut());
            if ($planning->isAllDay()) {
                $sheet->setCellValue('J' . $counter, 'Oui');
            } elseif ($planning->getStart()->format('H') > 12) {
                $sheet->setCellValue('J' . $counter, 'Après-midi');
            } else {
                $sheet->setCellValue('J' . $counter, 'Matin');
            }

//            $sheet->setCellValue('E' . $counter, $phoneNumber->getOffice());
            $counter++;
        }
//        // Create your Office 2007 Excel (XLSX Format)
        $writer = new Xlsx($spreadsheet);
//
//        // In this case, we want to write the file in the public directory
        $publicDirectory = $this->getContainer()->get('kernel')->getProjectDir() . '/web/xlsx';
//        // e.g /var/www/project/public/my_first_excel_symfony4.xlsx
        $excelFilepath = $publicDirectory . '/Planning du ' . $start_week . ' au ' . $end_week . '.xlsx';
////        $fileName = 'my_first_excel_symfony4.xlsx';
////        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
//        // Create the file
        $writer->save($excelFilepath);
//        $output->writeln($excelFilepath);
        // Return a text response to the browser saying that the excel was succesfully created
        $message->attach(\Swift_Attachment::fromPath($excelFilepath));

        $numSent += $this->getContainer()->get('mailer')->send($message, $failedRecipients);

        return new Response("Excel generated succesfully");

    }
}
