<?php


namespace TimSoft\GeneralBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceListener
{
    private $lockFilePath;
    private $twig;

    public function __construct($lockFilePath, \Twig_Environment $twig)
    {
        $this->lockFilePath = $lockFilePath;
        $this->twig = $twig;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
//        if (!file_exists($this->lockFilePath)) {
//            return;
//        }
//
//        $page = $this->twig->render('TimSoftGeneralBundle::maintenance.html.twig');
//
//        $event->setResponse(
//            new Response(
//                $page,
//                Response::HTTP_SERVICE_UNAVAILABLE
//            )
//        );
//        $event->stopPropagation();
    }
}