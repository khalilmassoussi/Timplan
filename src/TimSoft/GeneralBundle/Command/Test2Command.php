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
        foreach ($tc as $value) {
            $name = $value->getClient()->getRaisonSociale();
            $cl = $em->getRepository('TimSoftGeneralBundle:Client')->findOneByRaisonSociale($name);
            $output->writeln($cl->getRaisonSociale() . ' ' . $cl->getId());
            $value->setClient($cl);
            $em->persist($cl);
        }
        $em->flush();
    }
}
