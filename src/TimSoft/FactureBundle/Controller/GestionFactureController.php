<?php

namespace TimSoft\FactureBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use TimSoft\GeneralBundle\Entity\Facture;
use TimSoft\GeneralBundle\Entity\NotificationUtilisateur;
use TimSoft\NotificationBundle\Entity\Notification;

class GestionFactureController extends Controller
{

    /**
     * @Route("/AjouterFacture", name="AjouterFacture")
     */
    public function ajouterFactureAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $Facture = new Facture();
        $form = $this->createForm('TimSoft\GeneralBundle\Form\FactureType', $Facture, array('obligatoire' => true));
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
        }

        if ($form->isSubmitted() && $form->isValid()) {
//            /**
//             *  @var Symfony\Component\HttpFoundation\File\UploadedFile $file 
//             */
//            $file = $Facture->getFacturePDF();
//            $fileName = md5(uniqid()).'.'.$file->guessExtension(); // pour assurer que le nom d'une image soit unique
//            $file->move($this->getParameter('DossierFactures'),$fileName);
            $Facture = $form->getData();
//            $Facture->setFacturePDF($fileName);
            $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
            $FactureAvecMemeNumero = $em->getRepository('TimSoftGeneralBundle:Facture')->findOneBy(array('numeroFacture' => $Facture->getNumeroFacture()));
            if (!empty($FactureAvecMemeNumero)) {
                $form->get('numeroFacture')->addError(new FormError('Le numéro de la facture est déjà utilisé'));
            }
            if (empty($FactureAvecMemeNumero)) {
                $em->persist($Facture); //L'enregistrement se fait uniquement dans Doctrine
                $em->flush(); // pour sauvegarder les données dans la BD

                $notification = new Notification();
                $notification
                    ->setTitle('Nouvelle facture')
                    ->setDescription('Client: ' . $Facture->getClient())
                    ->setResponsable($this->getUser())
                    ->setRoute('ConsulterUneFacture')
                    ->setParameters(array('id' => $Facture->getId()));
                $em->persist($notification);
                $em->flush();

                $message = (new \Swift_Message('Nouvelle facture'))
                    ->setFrom(['kaoutherchtimsoft@gmail.com' => "Administrateur TimSoft"])
                    ->setBody(
                        $this->renderView(
                            '@TimSoftFacture/Emails/emailFacture.txt.twig', array('Facture' => $Facture)
                        ), 'text/html');
                $message->attach(\Swift_Attachment::fromPath($this->getParameter('DossierFactures') . '/' . $Facture->getFacturePDF()));

                $Admins = $em->getRepository('TimSoftGeneralBundle:Utilisateur')->findBy(array('roleUtilisateur' => "ROLE_ADMIN"));
                $Clients = $em->getRepository('TimSoftGeneralBundle:Utilisateur')->findBy(array('client' => $Facture->getClient()));
                foreach ($Admins as $user) {
                    $message->setTo([$user->getEmail() => $user->getNomUtilisateur() . ' ' . $user->getPrenomUtilisateur()]);
                    $this->get('mailer')->send($message);
                    $notificationUser = new NotificationUtilisateur();
                    $notificationUser
                        ->setNotification($notification)
                        ->setUtilisateur($user)
                        ->setVu(0);
                    $em->persist($notificationUser);
                    $em->flush();
                    $pusher = $this->get('mrad.pusher.notificaitons'); // Appel le service
                    $pusher->trigger($notificationUser);
                }
                foreach ($Clients as $user) {
                    $message->setTo([$user->getEmail() => $user->getNomUtilisateur() . ' ' . $user->getPrenomUtilisateur()]);
                    $this->get('mailer')->send($message);
                    $notificationUser = new NotificationUtilisateur();
                    $notificationUser
                        ->setNotification($notification)
                        ->setUtilisateur($user)
                        ->setVu(0);
                    $em->persist($notificationUser);
                    $em->flush();
                    $pusher = $this->get('mrad.pusher.notificaitons'); // Appel le service
                    $pusher->trigger($notificationUser);
                }


                $this->addFlash('OK-Ajout', 'La facture N°' . $Facture->getNumeroFacture() . ' est ajoutée avec succès !');
                return $this->redirectToRoute('ConsulterUneFacture', array('id' => $Facture->getId()));

            }
        }

        return $this->render('@TimSoftFacture/GestionFacture/AjouterFacture.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/ConsulterFactures", name="ConsulterFactures")
     */
    public function consulterFactureAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Factures = $em->getRepository('TimSoftGeneralBundle:Facture')->findAll();
        return $this->render('@TimSoftFacture/GestionFacture/ConsulterFacture.html.twig', array('Factures' => $Factures));
    }

    /**
     * @Route("/ConsulterFacturesType/{typeFacture}", name="ConsulterFacturesParType")
     */
    public function consulterFactureParTypeAction($typeFacture)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Factures = $em->getRepository('TimSoftGeneralBundle:Facture')->getFactureParType($typeFacture);
        return $this->render('@TimSoftFacture/GestionFacture/ConsulterFacture.html.twig', array('Factures' => $Factures, 'typeFacture' => $typeFacture));
    }

    /**
     * @Route("/ConsulterFacturesEnCours/", name="ConsulterFacturesEnCours")
     */
    public function consulterFactureEnCoursAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Factures = $em->getRepository('TimSoftGeneralBundle:Facture')->getFactureEnCours();
        return $this->render('@TimSoftFacture/GestionFacture/ConsulterFacture.html.twig', array('Factures' => $Factures));
    }

    /**
     * @Route("/MesFactures", name="MesFactures")
     */
    public function consulterMesFactureAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Factures = $em->getRepository('TimSoftGeneralBundle:Facture')->findBy(array('client' => $this->getUser()->getClient()));
        return $this->render('@TimSoftFacture/GestionFacture/ConsulterFacture.html.twig', array('Factures' => $Factures));
    }

    /**
     * @Route("/ConsulterUneFactures/{id}", name="ConsulterUneFacture")
     */
    public function consulterUneFactureAction($id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Facture = $em->getRepository("TimSoftGeneralBundle:Facture")->findOneBy(array('id' => $id));
        return $this->render('@TimSoftFacture/GestionFacture/ConsulterUneFacture.html.twig', array('Facture' => $Facture));
    }

    /**
     * @Route("/SupprimerUneFactures/{id}", name="SupprimerUneFacture")
     */
    public function supprimerUneFactureAction($id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Facture = $em->getRepository("TimSoftGeneralBundle:Facture")->findOneBy(array('id' => $id));
        return $this->render('@TimSoftFacture/GestionFacture/SupprimerUneFacture.html.twig', array('Facture' => $Facture));
    }

    /**
     * @Route("/SupprimerFactures/{id}", name="SupprimerFacture")
     */
    public function supprimerFactureAction($id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Facture = $em->getRepository("TimSoftGeneralBundle:Facture")->findOneBy(array('id' => $id));
//        $PDF = $Facture->getFacturePDF();
//        $Lien=$this->getParameter('DossierFactures').'/'.$PDF;
//        $FileSystem= new Filesystem();
//        $FileSystem->remove(array($Lien));
        $em->remove($Facture);
        $em->flush();
        $this->addFlash('OK-Suppression', 'La facture N°' . $Facture->getNumeroFacture() . ' a été supprimé avec succès !');
        return $this->redirectToRoute('ConsulterFactures');
    }

    /**
     * @Route("/ModifierFactures/{id}", name="ModifierFacture")
     */
    public function modifierFactureAction($id, Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Facture = $em->getRepository("TimSoftGeneralBundle:Facture")->findOneBy(array('id' => $id));

        $form = $this->createForm('TimSoft\GeneralBundle\Form\FactureType', $Facture, array('obligatoire' => false));
        // $form = $this->createForm('TimSoft\GeneralBundle\Form\FactureType', $Facture);
//        $PDFOriginal = $Facture->getFacturePDF();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
        }
        if ($form->isValid()) {
            $Facture = $form->getData();
            $FactureAvecMemeNumero = $em->getRepository('TimSoftGeneralBundle:Facture')->findOneBy(array('numeroFacture' => $Facture->getNumeroFacture()));
            if (!empty($FactureAvecMemeNumero) && $FactureAvecMemeNumero != $Facture) {
                $form->get('numeroFacture')->addError(new FormError('Le numéro de la facture est déjà utilisé'));
            } else {
//            var_dump($form->get('factureFile')->getData());
//            if ($Facture->getFacturePDF() != NULL )
//            {
//                 /**
//                 *  @var Symfony\Component\HttpFoundation\File\UploadedFile $file 
//                 */
//                 $file = $Facture->getFacturePDF();
//                $fileName = md5(uniqid()).'.'.$file->guessExtension(); // pour assurer que le nom d'une image soit unique
//                $file->move($this->getParameter('DossierFactures'),$fileName);
//                $Facture->setFacturePDF($fileName);
//                
//                $Lien=$this->getParameter('DossierFactures').'/'.$PDFOriginal;
//                $FileSystem= new Filesystem();
//                $FileSystem->remove(array($Lien));
//            }
//            elseif ($Facture->getFacturePDF() == NULL )
//            {
////                $fileName = md5(uniqid()).'.'.$PDFOriginal->guessExtension(); // pour assurer que le nom d'une image soit unique
////                $PDFOriginal->move($this->getParameter('DossierFactures'),$fileName);
////                $Facture->setFacturePDF($fileName);
//                $Facture->setFacturePDF($PDFOriginal);
//            }
//            $em =  $this->getDoctrine()->getManager(); // initialise la connexion à la BD
                $em->persist($Facture); //L'enregistrement se fait uniquement dans Doctrine
                $em->flush(); // pour sauvegarder les données dans la BD ,array ('client'=>$Client->getCodeClient()));
                $this->addFlash('OK-Modification', 'La facture N°' . $Facture->getNumeroFacture() . ' a été modifié avec succès !');
                return $this->redirectToRoute('ConsulterUneFacture', array('id' => $Facture->getId()));
            }
        }
        return $this->render('@TimSoftFacture/GestionFacture/ModifierFacture.html.twig', array('form' => $form->createView(), 'Facture' => $Facture));
    }

    /**
     * Les factures encours d'un client specifique
     * @Route("/MesFacturesEnCours", name="MesFacturesEnCours")
     */
    public function consulterMesFactureEnCoursAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Factures = $em->getRepository('TimSoftGeneralBundle:Facture')->getFactureEnCoursCleint($this->getUser()->getClient());
        return $this->render('@TimSoftFacture/GestionFacture/ConsulterFacture.html.twig', array('Factures' => $Factures));
    }

    /**
     * @Route("/ConsulterMesFacturesParType/{typeFacture}", name="ConsulterFacturesParTypeParClient")
     */
    public function consulterFactureParTypeParClientAction($typeFacture)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Factures = $em->getRepository('TimSoftGeneralBundle:Facture')->getFactureParStatutParClient($typeFacture, $this->getUser()->getClient());
        return $this->render('@TimSoftFacture/GestionFacture/ConsulterFacture.html.twig', array('Factures' => $Factures, 'typeFacture' => $typeFacture));
    }

    /**
     * @Route("/ModifStatutFacture/{id}", name="ModifFactModal")
     */
    public function modifierFactureModalAction($id, Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Facture = $em->getRepository("TimSoftGeneralBundle:Facture")->findOneBy(array('id' => $id));

        $form = $this->createForm('TimSoft\GeneralBundle\Form\FactureType', $Facture);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
        }

        if ($form->isValid()) {
            $Facture = $form->getData();
            if ($Facture->getStatutFacture() != "Refusée") {
                $Facture->setCauseRefusFacture(null);
            }
            $em->persist($Facture); //L'enregistrement se fait uniquement dans Doctrine
            $em->flush(); // pour sauvegarder les données dans la BD ,array ('client'=>$Client->getCodeClient()));
            if ($this->getUser()->getRoleUtilisateur() == "ROLE_CLIENT") {
                return $this->redirectToRoute('MesFactures');
            }
            return $this->redirectToRoute('ConsulterFactures');


        }
        $this->addFlash('OK-Modification', 'Le statut de la facture N°' . $Facture->getNumeroFacture() . ' a été modifié avec succès !');
        return $this->render('@TimSoftFacture/GestionFacture/ModifierFactureModal.html.twig', array('form' => $form->createView(), 'Facture' => $Facture));
    }


}
