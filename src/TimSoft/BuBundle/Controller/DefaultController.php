<?php

namespace TimSoft\BuBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $bus = $this->getDoctrine()->getRepository('TimSoftBuBundle:Bu')->findAll();
        return $this->render('@TimSoftBu/Default/index.html.twig', array('bus' => $bus));
    }

    public function usersAction()
    {
        return $this->render('@TimSoftBu/Default/modal.html.twig');
    }

    public function deleteUserAction($bu, $user)
    {
        $bu = $this->getDoctrine()->getRepository('TimSoftBuBundle:Bu')->find($bu);
        $user = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Utilisateur')->find($user);
        // var_dump($bu);
        $bu->remove($user);
        $user->removeBu($bu);
        $em = $this->getDoctrine()->getManager();
        $em->persist($bu);
        $em->flush();
        return $this->redirectToRoute('bu_index');
    }

    public function addUserAction($bu, $user)
    {
        $bu = $this->getDoctrine()->getRepository('TimSoftBuBundle:Bu')->find($bu);
        $user = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Utilisateur')->find($user);
        $em = $this->getDoctrine()->getManager();
        $bu->add($user);
        $user->addBu($bu);
        // var_dump($bu);
        $em->persist($bu);
        $em->flush();
        return $this->redirectToRoute('bu_index');
    }


}
