<?php

namespace TimSoft\GeneralBundle\Listener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class RedirectionListener
{

    public function __construct(ContainerInterface $container, Session $session)
    {
        $this->session = $session;
        $this->router = $container->get('router');
        $this->securityContext = $container->get('security.token_storage');
        $this->doctrine = $container->get('doctrine');
    }

    //c'est la methode definie dans le service (celle qui va etre appeler )
    public function onKernelRequest(GetResponseEvent $event)
    {
//        $Route = $event->getRequest()->attributes->get('_route');
//        $Utilisateur = $this->securityContext->getToken()->getUser();
//        // pour verifier si l'utilisateur est connecté
//        if (is_object($Utilisateur)) {
//
//            $Roles = $Utilisateur->getRoles();
//            $RoleUser = $Roles[0];
//
////            $Roles = $Utilisateur->getRoles();
////            foreach ($Roles as $Role) {
////                if ($Role != 'ROLE_USER') {
////                    $RoleUser = $Role;
////                }
////            }
//            $em = $this->doctrine->getManager();
//            if (!$em->getRepository('TimSoftGeneralBundle:DroitAcces')->findByRouteDroit($Route)) {
//                return true;
//            } elseif ($em->getRepository('TimSoftGeneralBundle:DroitAccesPersonne')->getAutorisationPersonne($Route, $Utilisateur->getId())) {
//                return true;
//            } elseif ($em->getRepository('TimSoftGeneralBundle:DroitAccesGroupe')->getAutorisationGroupe($Route, $RoleUser) /*|| $Route == 'TelechargerFeuillePresence'*/) {
////                print_r($Route);
////                die();
//                return true;
//            } else {
//                $this->session->getFlashBag()->add('notice', 'Vous n\'êtes pas autorisé à accéder a cette ressource, Vous serez redirigé vers la page d\'accueilssssssssss !' . $Route);
//                $event->setResponse(new RedirectResponse($this->router->generate('tim_soft_client_homepage')));
//            }
//
//        }
    }

}
