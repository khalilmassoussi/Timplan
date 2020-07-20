<?php

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            new TimSoft\UtilisateurBundle\TimSoftUtilisateurBundle(),
            new TimSoft\ClientBundle\TimSoftClientBundle(),
            new TimSoft\GeneralBundle\TimSoftGeneralBundle(),
            new FOS\RestBundle\FOSRestBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new Nelmio\ApiDocBundle\NelmioApiDocBundle(),
            new TimSoft\FactureBundle\TimSoftFactureBundle(),
            new TimSoft\FeuilleRapportInterventionBundle\TimSoftFeuilleRapportInterventionBundle(),
            new Vich\UploaderBundle\VichUploaderBundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new TimSoft\DroitAccesBundle\TimSoftDroitAccesBundle(),
            new SBC\NotificationsBundle\NotificationsBundle(),
            new TimSoft\NotificationBundle\TimSoftNotificationBundle(),
            new Liuggio\ExcelBundle\LiuggioExcelBundle(),
            new TimSoft\BuBundle\TimSoftBuBundle(),
            new TimSoft\CommandeBundle\TimSoftCommandeBundle(),
            new CalendarBundle\CalendarBundle(),
//            new ActivityLogBundle\ActivityLogBundle(),
            new TimSoft\TasksBundle\TimSoftTasksBundle(),
            new Evercode\DependentSelectBundle\DependentSelectBundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();

            if ('dev' === $this->getEnvironment()) {
                $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
                $bundles[] = new Symfony\Bundle\WebServerBundle\WebServerBundle();
            }
        }

        return $bundles;
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        return dirname(__DIR__) . '/var/cache/' . $this->getEnvironment();
    }

    public function getLogDir()
    {
        return dirname(__DIR__) . '/var/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(function (ContainerBuilder $container) {
            $container->setParameter('container.autowiring.strict_mode', true);
            $container->setParameter('container.dumper.inline_class_loader', true);

            $container->addObjectResource($this);
        });
        $loader->load($this->getRootDir() . '/config/config_' . $this->getEnvironment() . '.yml');
    }
}
