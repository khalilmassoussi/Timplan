<?php

namespace TimSoft\NotificationBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class NotificationController extends Controller
{

    /**
     * @Route("/ChangerStatutNotification", name="ChangerStatutNotification")
     * @Method({"POST"})
     */
    public function changerVuNotificationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Notification = $em->getRepository("TimSoftGeneralBundle:NotificationUtilisateur")->findOneBy(array('id' => $request->get('id')));
        $Notification->setVu(1);
        $em->persist($Notification);
        $em->flush();
       // $response = new JsonResponse();
        return new JsonResponse('errr');
    }


    /**
     * @Route("/Notifications", name="Notification")
     */
    public function afficherNotificationAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Notifications = $em->getRepository("TimSoftNotificationBundle:Notification")->NotificationUtilisateurTrie($this->getUser());
        $NotificationsVu = $em->getRepository("TimSoftGeneralBundle:NotificationUtilisateur")->findBy(array('utilisateur' => $this->getUser(), 'vu' => 1));
        $NotificationsNonVu = $em->getRepository("TimSoftGeneralBundle:NotificationUtilisateur")->findBy(array('utilisateur' => $this->getUser(), 'vu' => 0));

        return $this->render('@TimSoftNotification/Notification/Notification.html.twig', array(
            'Notifications' => $Notifications, 'NotificationsVu' => $NotificationsVu, 'NotificationNonVu' => $NotificationsNonVu));
    }

    /**
     * @Route("/UneNotification/{id}", name="UneNotification")
     */
    public function consulterUneNotificationAction($id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Notification = $em->getRepository("TimSoftNotificationBundle:Notification")->findOneBy(array('id' => $id));
//        var_dump($Notification);
//        die();
        return $this->render('@TimSoftNotification/Notification/ConsulterUneNotification.html.twig', array('Notification' => $Notification));

    }


    /**
     * @Route("/ToutesLesNotifications", name="AllNotifications")
     */
    public function allNotificationAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Notifications = $em->getRepository("TimSoftNotificationBundle:Notification")->NotificationUtilisateurTrie($this->getUser());
        return $this->render('@TimSoftNotification/Notification/ConsulterNotification.html.twig', array(
            'Notifications' => $Notifications));
    }
}
