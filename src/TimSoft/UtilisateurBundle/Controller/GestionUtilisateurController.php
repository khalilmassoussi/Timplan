<?php

namespace TimSoft\UtilisateurBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use TimSoft\UtilisateurBundle\Security\UtilisateurVoter;

class GestionUtilisateurController extends Controller
{

    public function consulterUtilisateurAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Utilisateurs = $em->getRepository('TimSoftGeneralBundle:Utilisateur')->findAll();
        return $this->render('@TimSoftUtilisateur/GestionUtilisateur/ConsulterUtilisateur.html.twig', array('Utilisateurs' => $Utilisateurs));
    }

    public function consulterUtilisateurActifAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Utilisateurs = $em->getRepository('TimSoftGeneralBundle:Utilisateur')->findBy(array('statutProfil' => 1, 'enabled' => 1, 'confirmationToken' => null));
        return $this->render('@TimSoftUtilisateur/GestionUtilisateur/ConsulterUtilisateur.html.twig', array('Utilisateurs' => $Utilisateurs));
    }

    public function consulterUtilisateurDeactiveAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Utilisateurs = $em->getRepository('TimSoftGeneralBundle:Utilisateur')->findBy(array('statutProfil' => 0));
        return $this->render('@TimSoftUtilisateur/GestionUtilisateur/ConsulterUtilisateur.html.twig', array('Utilisateurs' => $Utilisateurs));
    }

    public function consulterUtilisateurInvalidAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Utilisateurs = $em->getRepository('TimSoftGeneralBundle:Utilisateur')->findBy(array('statutProfil' => 1, 'enabled' => 0));
        return $this->render('@TimSoftUtilisateur/GestionUtilisateur/ConsulterUtilisateur.html.twig', array('Utilisateurs' => $Utilisateurs));
    }

    public function consulterUnUtilisateurAction($id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Utilisateur = $em->getRepository("TimSoftGeneralBundle:Utilisateur")->findOneBy(array('id' => $id));
//        // check for "view" access: calls all voters
//        $this->denyAccessUnlessGranted('view', $Utilisateur);
        if ($this->isGranted(UtilisateurVoter::VIEW, $Utilisateur)) {
            return $this->render('@TimSoftUtilisateur/GestionUtilisateur/ConsulterUnUtilisateur.html.twig', array('utilisateur' => $Utilisateur));
        } else {
            $this->addFlash('KO-ConsulterUtilisateur', 'Vous n\'êtes pas autorisé à accéder a cette ressource, Vous serez redirigé vers la page d\'accueil !');
            return $this->redirectToRoute('tim_soft_client_homepage');
        }
    }

    public function supprimerUnUtilisateurAction($id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Utilisateur = $em->getRepository("TimSoftGeneralBundle:Utilisateur")->findOneBy(array('id' => $id));
        return $this->render('@TimSoftUtilisateur/GestionUtilisateur/SupprimerUnUtilisateur.html.twig', array('utilisateur' => $Utilisateur));
    }

    public function supprimerUtilisateurAction($id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $utilisateur = $em->getRepository("TimSoftGeneralBundle:Utilisateur")->findOneBy(array('id' => $id));
        $em->remove($utilisateur);
        $em->flush();
        $this->addFlash('OK-Suppression', $utilisateur->getNomUtilisateur() . ' ' . $utilisateur->getPrenomUtilisateur() . ' est supprimé avec succès !! ');
        return $this->redirectToRoute('ConsulterUtilisateur');
    }

    public function deactiverUnUtilisateurAction($id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Utilisateur = $em->getRepository("TimSoftGeneralBundle:Utilisateur")->findOneBy(array('id' => $id));
        return $this->render('@TimSoftUtilisateur/GestionUtilisateur/DeactiverUnUtilisateur.html.twig', array('utilisateur' => $Utilisateur));
    }

    public function deactiverUtilisateurAction($id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Utilisateur = $em->getRepository("TimSoftGeneralBundle:Utilisateur")->findOneBy(array('id' => $id));
        $Utilisateur->setStatutProfil(false);
        $em->persist($Utilisateur); //L'enregistrement se fait uniquement dans Doctrine
        $em->flush(); // pour sauvegarder les données dans la BD
        $this->addFlash('OK-Deactivation', 'Le profil de ' . $Utilisateur->getNomUtilisateur() . ' ' . $Utilisateur->getPrenomUtilisateur() . ' a été désactivé !!');
        return $this->redirectToRoute('ConsulterUtilisateur');
        //return $this->redirectToRoute('ConsulterUnClient',array ('codeClient'=>$Client->getCodeClient()));
        //return $this->redirectToRoute('@TimSoftUtilisateur/GestionUtilisateur/ConsulterUtilisateur.html.twig');
    }

    public function reactiverUtilisateurAction($id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Utilisateur = $em->getRepository("TimSoftGeneralBundle:Utilisateur")->findOneBy(array('id' => $id));
        $Utilisateur->setStatutProfil(true);
        $em->persist($Utilisateur); //L'enregistrement se fait uniquement dans Doctrine
        $em->flush(); // pour sauvegarder les données dans la BD
        $this->addFlash('OK-Reactivation', 'Le profil de ' . $Utilisateur->getNomUtilisateur() . ' ' . $Utilisateur->getPrenomUtilisateur() . ' est activé de nouveau !!');
        return $this->redirectToRoute('ConsulterUtilisateur');
        //return $this->redirectToRoute('ConsulterUnClient',array ('codeClient'=>$Client->getCodeClient()));
        //return $this->redirectToRoute('@TimSoftUtilisateur/GestionUtilisateur/ConsulterUtilisateur.html.twig');
    }

    public function reconfirmationMailUtilisateurAction($confirmationModifMail)
    {
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Utilisateur = $em->getRepository('TimSoftGeneralBundle:Utilisateur')->findOneBy(array('confirmationModifMail' => $confirmationModifMail));
        if (null === $Utilisateur) {
            throw new NotFoundHttpException(sprintf('Aucun utilisateur avec cette confirmation'));
        }
        $Utilisateur->setEnabled(1);
        $Utilisateur->setConfirmationModifMail(null);
//        $Utilisateur->setPassword($Utilisateur->getPassword());
        $em->persist($Utilisateur); //L'enregistrement se fait uniquement dans Doctrine
        $em->flush();
        $this->addFlash('OK-ReconfirmationMail', 'Merci d\'avoir confirmer votre nouvelle adresse e-mail, votre compte est de nouveau actif!! ');
//        return $this->redirectToRoute('fos_user_registration_confirmed');
        return $this->redirectToRoute('tim_soft_client_homepage');
    }

    public function getUsersAction()
    {
        $current = $this->getUser()->getBus();
        $users = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Utilisateur')->findNotInBu($current);
        return new JsonResponse($users);
    }

    public function getAllUsersAction()
    {
        $users = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Utilisateur')->findAll();
        return new JsonResponse($users);
    }

    public function getBySocieteAction(\Symfony\Component\HttpFoundation\Request $request)
    {
        $client = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Client')->find($request->get('id'));
        $users = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Utilisateur')->findBySociete($client);
        return new JsonResponse($users);
    }

    public function getByBusAction(\Symfony\Component\HttpFoundation\Request $request)
    {
        $users = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Utilisateur')->findByBu($request->get('id'));
        return new JsonResponse($users);
    }
}
