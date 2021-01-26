<?php

namespace TimSoft\GeneralBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Test2Command extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('test2')
            ->setDescription('...')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $tc = $em->getRepository('TimSoftCommandeBundle:TeteCommande')->findAll();
        $te = $em->getRepository('TimSoftTasksBundle:TaskEvent')->findAll();
        $arr = [];
        foreach ($tc as $value) {
            $client = $value->getClient();
            $name = $value->getClient()->getRaisonSociale();
            $cl = $em->getRepository('TimSoftGeneralBundle:Client')->findOneByRaisonSociale($name);
            $value->setClient($cl);
            $em->persist($cl);
        }
        foreach ($te as $value) {
            $client = $value->getClient();
            $name = $value->getClient()->getRaisonSociale();
            $cl = $em->getRepository('TimSoftGeneralBundle:Client')->findOneByRaisonSociale($name);
            $value->setClient($cl);
            $em->persist($cl);
        }
        $em->flush();
        foreach ($em->getRepository('TimSoftGeneralBundle:Client')->findAll() as $client) {
            if ((count($client->getTasks()) == 0 && count($client->getCommandes()) == 0) && $client->getRaisonSociale() != 'TimSoft') {
                $output->writeln('-----------------------------------------------');
                $output->writeln($client->getRaisonSociale() . ' ' . $client->getId());
                $em->remove($client);
            }
        }
        $em->flush();
    }
}
