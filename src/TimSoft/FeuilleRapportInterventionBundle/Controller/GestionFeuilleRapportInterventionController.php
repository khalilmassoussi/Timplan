<?php

namespace TimSoft\FeuilleRapportInterventionBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Dompdf\Dompdf;
use Dompdf\Options;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use TimSoft\FeuilleRapportInterventionBundle\Security\FeuilleVoter;
use TimSoft\FeuilleRapportInterventionBundle\Security\RapportVoter;
use TimSoft\GeneralBundle\Entity\FeuilleDePresence;
use TimSoft\GeneralBundle\Entity\NotificationUtilisateur;
use TimSoft\GeneralBundle\Entity\RapportIntervention;
use TimSoft\NotificationBundle\Entity\Notification;


class GestionFeuilleRapportInterventionController extends Controller
{

    /**
     * @Route("/RemplirFeuillePresence", name="RemplirFeuillePresence",options = { "expose" = true })
     */
    public function remplirFeuillePresenceAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        $FeuilleDePresence = new FeuilleDePresence();
        $RapportIntervention = new RapportIntervention();
        $em = $this->getDoctrine()->getManager();
        $planning = $em->getRepository('TimSoftGeneralBundle:Planning')->find($request->get('id'));

        $FeuilleDePresence->setHeureDebutInterventionMatin((new \DateTime())->setTime('09', '00'));
        $FeuilleDePresence->setHeureFinInterventionMatin((new \DateTime())->setTime('12', '00'));
        $FeuilleDePresence->setHeureDebutInterventionAM((new \DateTime())->setTime('14', '00'));
        $FeuilleDePresence->setHeureFinInterventionAM((new \DateTime())->setTime('17', '00'));
        $FeuilleDePresence->setIntervenant($planning->getUtilisateur());
        $FeuilleDePresence->setNumeroCommande($planning->getLc()->getCommande()->getnCommande());
        $FeuilleDePresence->setClient($planning->getLc()->getCommande()->getClient());
        $FeuilleDePresence->setLibelleCommande($planning->getLc()->getLibelle());
        $FeuilleDePresence->setStatutValidation(0);
        $FeuilleDePresence->setModeIntervention($planning->getLieu());
        $FeuilleDePresence->setDateIntervention($planning->getStart());
        $FeuilleDePresence->setIntervention($planning);
        if ($planning->getLieu() === "Sur site") {
            $FeuilleDePresence->setLieuIntervention($planning->getLc()->getCommande()->getClient()->getAdresseClient());
        }
        if ($planning->getLieu() === "à distance") {
            $FeuilleDePresence->setLieuIntervention("Tunis");
        }
        $em->persist($planning);
        $em->persist($FeuilleDePresence);
        //generation PDF
        $view = $this->renderView('@TimSoftFeuilleRapportIntervention/Telechargement/TelechargerUneFeuillePresence.html.twig', array('Feuille' => $FeuilleDePresence));
        // On crée une  instance pour définir les options de notre fichier pdf
        $options = new Options();
        // Pour simplifier l'affichage des images, on autorise dompdf à utiliser
        // des  url pour les nom de  fichier
        $options->set('isRemoteEnabled', true);
        $d = new Dompdf($options);
        $contxt = stream_context_create([
            'ssl' => ['verify_peer' => FALSE, 'verify_peer_name' => FALSE, 'allow_self_signed' => TRUE]]);
        $d->setHttpContext($contxt);
        $d->loadHtml($view);
        // Render the HTML as PDF
        $d->render();
        $fileName = 'Feuille de présence du ' . $FeuilleDePresence->getDateIntervention()->format('d-m-Y') . ' par ' . $FeuilleDePresence->getIntervenant() . ' pour ' . $FeuilleDePresence->getClient();
        $fileName = str_replace('/', '', $fileName);
        $directory = $this->get('kernel')->getRootDir() . '/../web/PDF/' . $fileName . '.pdf';
        $pdf_string = $d->output();
        file_put_contents($directory, $pdf_string);
        $RapportIntervention->setFeuilleDePresence($FeuilleDePresence);
        $em->persist($RapportIntervention);
        $em->flush();
        return new JsonResponse([$FeuilleDePresence, $RapportIntervention, $planning]);

    }


    /**
     * @Route("/InfoClient/{id}", name="InfoClient" ,options={"expose"=true})
     */
    public function consulterInfoClientAction(Request $request, $id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
            $Client = $em->getRepository("TimSoftGeneralBundle:Client")->findOneBy(array('id' => $id));
            $response = new JsonResponse();
            return $response->setData(array('codeClient' => $Client->getCodeClient(), 'telephone' => $Client->getTelephone(), 'adresseClient' => $Client->getAdresseClient()));
        }
    }

    /**
     * @Route("/InfoIntervention/{id}", name="InfoIntervention",options={"expose"=true})
     */
    public function consulterInfoInterventionAction(Request $request, $id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
            $Intervention = $em->getRepository("TimSoftGeneralBundle:FeuilleDePresence")->findOneBy(array('id' => $id));
            $response = new JsonResponse();
            return $response->setData(array('raisonSociale' => $Intervention->getClient()->getRaisonSociale(), 'codeClient' => $Intervention->getClient()->getCodeClient(), 'telephone' => $Intervention->getClient()->getTelephone(), 'adresseClient' => $Intervention->getClient()->getAdresseClient(),
                'dateIntervention' => $Intervention->getDateIntervention(), 'lieuIntervention' => $Intervention->getLieuIntervention(),
                'nomIntervenant' => $Intervention->getIntervenant()->getNomUtilisateur(), 'prenomIntervenant' => $Intervention->getIntervenant()->getPrenomUtilisateur(), 'qualiteIntervenant' => 'Consultant(e)'));
        }
    }

    /**
     * @Route("/ConsulterFeuillesRapportIntervention", name="ConsulterFeuillesRapportIntervention")
     */
    public function consulterFeuillesRapportInterventionAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('@TimSoftFeuilleRapportIntervention/GestionFeuilleRapportIntervention/ConsulterFeuilleRapportIntervention.html.twig');
    }

    /**
     * @Route("/MesFeuillesRapportIntervention", name="ConsulterFeuillesRapportInterventionParClient")
     */
    public function consulterFeuillesRapportInterventionParClientAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        return $this->render('@TimSoftFeuilleRapportIntervention/GestionFeuilleRapportIntervention/ConsulterFeuilleRapportIntervention.html.twig', array('parClient' => 'parClient'));
    }

    /**
     * @Route("/MesIntervention", name="ConsulterFeuillesRapportInterventionParConsultant")
     */
    public function consulterFeuillesRapportInterventionParConsultantAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        return $this->render('@TimSoftFeuilleRapportIntervention/GestionFeuilleRapportIntervention/ConsulterFeuilleRapportIntervention.html.twig', array('ParConsultant' => 'ParConsultant'));
    }

    /**
     * @Route("/ConsulterFeuillePresence/{id}", name="ConsulterFeuillePresence" ,options = { "expose" = true })
     */
    public function consulterUneFeuillePresenceAction($id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Feuille = $em->getRepository("TimSoftGeneralBundle:FeuilleDePresence")->findOneBy(array('id' => $id));
        return $this->render('@TimSoftFeuilleRapportIntervention/GestionFeuilleRapportIntervention/ConsulterUneFeuillePresence.html.twig', array('Feuille' => $Feuille));
        $this->addFlash('KO-ConsulterUtilisateur', 'Vous n\'êtes pas autorisé à accéder a cette ressource, Vous serez redirigé vers la page d\'accueil !');
        return $this->redirectToRoute('tim_soft_client_homepage');
    }

    /**
     * @Route("/ConsulterRapportIntervention/{id}", name="ConsulterRapportIntervention" , options = { "expose" = true },)
     */
    public function consulterUnRapportAction($id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Rapport = $em->getRepository("TimSoftGeneralBundle:RapportIntervention")->findOneBy(array('id' => $id));
//        if ($this->isGranted(RapportVoter::VIEW, $Rapport)) {
        return $this->render('@TimSoftFeuilleRapportIntervention/GestionFeuilleRapportIntervention/ConsulterUnRapport.html.twig', array('Rapport' => $Rapport));
//        }
        //        } else {
        $this->addFlash('KO-ConsulterUtilisateur', 'Vous n\'êtes pas autorisé à accéder a cette ressource, Vous serez redirigé vers la page d\'accueil !');
        return $this->redirectToRoute('tim_soft_client_homepage');
//        }
    }

    /**
     * @Route("/SupprimerUneFeuille/{id}", name="SupprimerUneFeuille", options = { "expose" = true },)
     */
    public function supprimerUneFeuilleDePresenceAction($id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Feuille = $em->getRepository("TimSoftGeneralBundle:FeuilleDePresence")->findOneBy(array('id' => $id));
        if ($Feuille->getStatutValidation() == 0) {
            if ($this->isGranted(FeuilleVoter::DELETE, $Feuille)) {
//                $this->addFlash('OK-Suppression', 'La feuille de présence est supprimée avec succès  !');
                return $this->render('@TimSoftFeuilleRapportIntervention/GestionFeuilleRapportIntervention/SupprimerUneFeuille.html.twig', array('Feuille' => $Feuille));
            } else {
                $this->addFlash('KO-ConsulterUtilisateur', 'Vous n\'êtes pas autorisé à accéder a cette ressource, Vous serez redirigé vers la page d\'accueil !');
                return $this->redirectToRoute('tim_soft_client_homepage');
            }
        } else {
            $this->addFlash('FeuilleValide', 'La Feuille est déjà validée, vous ne pouvez plus la supprimer !');
            return $this->redirectToRoute('ConsulterFeuillePresence', array('id' => $Feuille->getId()));
        }
    }

    /**
     * @Route("/SupprimerFeuille/{id}", name="SupprimerFeuille" , options = { "expose" = true },)
     */
    public function supprimerFeuilleAction($id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Feuille = $em->getRepository("TimSoftGeneralBundle:FeuilleDePresence")->findOneBy(array('id' => $id));
        if ($Feuille->getStatutValidation() == 0) {
            if ($this->isGranted(FeuilleVoter::DELETE, $Feuille)) {
                $em->remove($Feuille);
                $em->flush();
                $this->addFlash('OK-Suppression', 'La feuille de présence est supprimée avec succès  !');
                return $this->redirectToRoute('ConsulterFeuillesRapportIntervention');
            } else {
                $this->addFlash('KO-ConsulterUtilisateur', 'Vous n\'êtes pas autorisé à accéder a cette ressource, Vous serez redirigé vers la page d\'accueil !');
                return $this->redirectToRoute('tim_soft_client_homepage');
            }
        } else {
            $this->addFlash('FeuilleValide', 'La Feuille est déjà validée, vous ne pouvez plus la supprimer !');
            return $this->redirectToRoute('ConsulterFeuillePresence', array('id' => $Feuille->getId()));
        }
    }

    /**
     * @Route("/SupprimerUnRapport/{id}", name="SupprimerUnRapport" , options = { "expose" = true },)
     */
    public function supprimerUnRapportAction($id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Rapport = $em->getRepository("TimSoftGeneralBundle:RapportIntervention")->findOneBy(array('id' => $id));
        if ($Rapport->getConfirmationDeInterventionParClient() == 0) {
            if ($this->isGranted(RapportVoter::DELETE, $Rapport)) {
                return $this->render('@TimSoftFeuilleRapportIntervention/GestionFeuilleRapportIntervention/SupprimerUnRapport.html.twig', array('Rapport' => $Rapport));
            } else {
                $this->addFlash('KO-ConsulterUtilisateur', 'Vous n\'êtes pas autorisé à accéder a cette ressource, Vous serez redirigé vers la page d\'accueil !');
                return $this->redirectToRoute('tim_soft_client_homepage');
            }
        } else {
            $this->addFlash('RapportValide', 'Le rapport est déjà validé, vous ne pouvez plus la supprimer !');
            return $this->redirectToRoute('ConsulterRapportParClient', array('id' => $Rapport->getId()));
        }
    }

    /**
     * @Route("/SupprimerRapport/{id}", name="SupprimerRapport" , options = { "expose" = true },)
     */
    public function supprimerRapportAction($id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Rapport = $em->getRepository("TimSoftGeneralBundle:RapportIntervention")->findOneBy(array('id' => $id));
        if ($Rapport->getConfirmationDeInterventionParClient() == 0) {
            if ($this->isGranted(RapportVoter::DELETE, $Rapport)) {
                $em->remove($Rapport);
                $em->flush();
                $this->addFlash('OK-Suppression', 'Le rapport d\'intervention est supprimé avec succès  !');
                return $this->redirectToRoute('ConsulterFeuillesRapportIntervention');
            } else {
                $this->addFlash('KO-ConsulterUtilisateur', 'Vous n\'êtes pas autorisé à accéder a cette ressource, Vous serez redirigé vers la page d\'accueil !');
                return $this->redirectToRoute('tim_soft_client_homepage');
            }
        } else {
            $this->addFlash('RapportValide', 'Le rapport est déjà validé, vous ne pouvez plus la supprimer !');
            return $this->redirectToRoute('ConsulterRapportParClient', array('id' => $Rapport->getId()));
        }
    }

    /**
     * @Route("/ModifierFeuillePresence/{id}", name="ModifierFeuillePresence", options = { "expose" = true },)
     *
     */
    public function modifierFeuillePresenceAction($id, Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Feuille = $em->getRepository("TimSoftGeneralBundle:FeuilleDePresence")->findOneBy(array('id' => $id));

        $form = $this->createForm('TimSoft\GeneralBundle\Form\FeuilleDePresenceType', $Feuille);
        $originalParts = new ArrayCollection();
        if ($Feuille->getParticipants()) {
            foreach ($Feuille->getParticipants() as $part) {
                $originalParts->add($part);
            }
        }
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
        }

        if ($form->isValid()) {
            foreach ($originalParts as $part) {
                if (false === $Feuille->getParticipants()->contains($part)) {
//                    $em->persist($part);
                    $em->remove($part);
                }
            }
            $failedRecipients = [];
            $numSent = 0;
            $em->persist($Feuille); //L'enregistrement se fait uniquement dans Doctrine
            $em->flush(); // pour sauvegarder les données dans la BD
            $Client = $Feuille->getIntervention()->getConfirmePar();
            $bu = $Feuille->getIntervention()->getLc()->getBu();
            if ($form["validationClient"]->getData() == 1) {
                // $em->persist($Feuille->getIntervention());
                if (!$Feuille->getIntervention()->getConfirmePar()) {
                    $Feuille->setValidationClient(null);
                    $Feuille->getIntervention()->setStatut('Confirmé');
                    $em->persist($Feuille);
                    $em->flush();
                    $this->addFlash('success', 'Il faut renseigner le nom du chef de projet dans le détail de l’intervention');
                    return $this->render('@TimSoftFeuilleRapportIntervention/GestionFeuilleRapportIntervention/ModifierFeuillePresence.html.twig', array('form' => $form->createView(), 'Feuille' => $Feuille));
                } else {
                    if ($form["statutValidation"]->getData() == 1) {
                        $this->valider($Feuille);
                        return $this->redirectToRoute('ModifierRapportIntervention', array('id' => $Feuille->getRapportIntervention()->getId()));
                    } else {
                        $messageRedaction = (new \Swift_Message('En attente de validation : Intervention ' . $Feuille->getClient()->getRaisonSociale() . ' du ' . $Feuille->getDateIntervention()->format("d/m/Y") . ' assuré par ' . $Feuille->getIntervenant()->getNomUtilisateur() . ' ' . $Feuille->getIntervenant()->getPrenomUtilisateur()))
                            ->setFrom(['timplan@timsoft.net' => "Administrateur TimSoft"])
                            ->setBody(
                                $this->renderView(
                                    '@TimSoftFeuilleRapportIntervention/Emails/redactionFeuille.txt.twig', array('Feuille' => $Feuille)
                                ), 'text/html');
                        $messageRedaction->setTo([$Client->getEmail() => $Client->getNomUtilisateur() . ' ' . $Client->getPrenomUtilisateur()]);
                        $numSent += $this->get('mailer')->send($messageRedaction, $failedRecipients);
                    }
                }
            } else {
                $this->valider($Feuille);
            }
            return $this->redirectToRoute('ModifierRapportIntervention', array('id' => $Feuille->getRapportIntervention()->getId()));
        }
        return $this->render('@TimSoftFeuilleRapportIntervention/GestionFeuilleRapportIntervention/ModifierFeuillePresence.html.twig', array('form' => $form->createView(), 'Feuille' => $Feuille));

    }


    /**
     * @Route("/ModifierRapportIntervention/{id}", name="ModifierRapportIntervention" , options = { "expose" = true },)
     */
    public function modifierRapportInterventionAction($id, Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Rapport = $em->getRepository("TimSoftGeneralBundle:RapportIntervention")->findOneBy(array('id' => $id));
        if ($Rapport->getFeuilleDePresence()->getStatutValidation() || $Rapport->getFeuilleDePresence()->getValidationClient()) {
            if ($Rapport->getConfirmationDeInterventionParClient() == 0) {
                $form = $this->createForm('TimSoft\GeneralBundle\Form\RapportInterventionType', $Rapport);
                $originalFiles = new ArrayCollection();
                foreach ($Rapport->getFichiersJoin() as $Fichier) {
                    $originalFiles->add($Fichier);
                }
                if ($request->isMethod('POST')) {
                    $form->handleRequest($request);
                }
                if ($form->isValid()) {
                    foreach ($originalFiles as $File) {
                        if (false === $Rapport->getFichiersJoin()->contains($File)) {
                            $em->persist($File);
                            $em->remove($File);
                        }
                    }
                    $redaction = "";
                    $em->persist($Rapport); //L'enregistrement se fait uniquement dans Doctrine
                    $em->flush(); // pour sauvegarder les données dans la BD
                    $message = (new \Swift_Message('Modification d\'un rapport d\'intervention'))
                        ->setFrom(['timplan@timsoft.net' => "Administrateur TimSoft"])
                        ->setBody(
                            $this->renderView(
                                '@TimSoftFeuilleRapportIntervention/Emails/emailRapport.txt.twig', array('Rapport' => $Rapport)
                            ), 'text/html');
                    $failedRecipients = [];
                    $numSent = 0;
                    foreach ($Rapport->getFichiersJoin() as $Fichier) {
                        if ($Fichier->getSendParMail()) {
                            $message->attach(\Swift_Attachment::fromPath($this->getParameter('DossierFichiersJoin') . '/' . $Fichier->getContenuFichierJoin()));
                        }
                    }
                    $view = $this->renderView('@TimSoftFeuilleRapportIntervention/Telechargement/TelechargerUnRapport.html.twig', array('Rapport' => $Rapport));
                    // On crée une  instance pour définir les options de notre fichier pdf
                    $options = new Options();
                    // Pour simplifier l'affichage des images, on autorise dompdf à utiliser
                    // des  url pour les nom de  fichier
                    $options->set('isRemoteEnabled', true);
                    $d = new Dompdf($options);
                    $contxt = stream_context_create([
                        'ssl' => [
                            'verify_peer' => FALSE,
                            'verify_peer_name' => FALSE,
                            'allow_self_signed' => TRUE
                        ]
                    ]);
                    $d->setHttpContext($contxt);
                    $d->loadHtml($view);
                    // Render the HTML as PDF
                    $d->render();
                    $fileName = 'Rapport de l\'intervention du ' . $Rapport->getFeuilleDePresence()->getDateIntervention()->format('d-m-Y') . ' par ' . $Rapport->getFeuilleDePresence()->getIntervenant() . ' Client: ' . $this->clean($Rapport->getFeuilleDePresence()->getClient());
                    $fileName = str_replace('/', '', $fileName);
                    $directory = $this->get('kernel')->getRootDir() . '/../web/PDF/' . $fileName . '.pdf';
                    $pdf_string = $d->output();
                    file_put_contents($directory, $pdf_string);
                    /* ------------------------------------- */
                    //generation PDF
                    $Feuille = $Rapport->getFeuilleDePresence();
                    $viewF = $this->renderView('@TimSoftFeuilleRapportIntervention/Telechargement/TelechargerUneFeuillePresence.html.twig', array('Feuille' => $Feuille));
                    // On crée une  instance pour définir les options de notre fichier pdf
                    $optionsF = new Options();
                    // Pour simplifier l'affichage des images, on autorise dompdf à utiliser
                    // des  url pour les nom de  fichier
                    $optionsF->set('isRemoteEnabled', true);
                    $dF = new Dompdf($optionsF);
                    $contxtF = stream_context_create([
                        'ssl' => ['verify_peer' => FALSE, 'verify_peer_name' => FALSE, 'allow_self_signed' => TRUE]]);
                    $dF->setHttpContext($contxtF);
                    $dF->loadHtml($viewF);
                    // Render the HTML as PDF
                    $dF->render();
                    $fileNameF = 'Feuille de présence du ' . $Feuille->getDateIntervention()->format('d-m-Y') . ' par ' . $Feuille->getIntervenant() . ' ' . $this->clean($Feuille->getClient());
                    $fileNameF = str_replace('/', '', $fileNameF);
                    $directoryF = $this->get('kernel')->getRootDir() . '/../web/PDF/' . $fileNameF . '.pdf';
                    $pdf_stringF = $dF->output();
                    file_put_contents($directoryF, $pdf_stringF);
                    /* --------------------- */
                    $validation = new Notification();
                    $validation
                        ->setTitle("Validation du rapport d'intervention")
                        ->setDescription('Client: ' . $Rapport->getFeuilleDePresence()->getClient() . '- Intervenant: ' . $Rapport->getFeuilleDePresence()->getIntervenant())
                        ->setResponsable($this->getUser())
                        ->setRoute('ConsulterRapportIntervention')
                        ->setParameters(array('id' => $Rapport->getId()));
                    $em->persist($validation);
                    $em->flush();
                    $bu = $Rapport->getFeuilleDePresence()->getIntervention()->getLc()->getBu();

                    if ($Rapport->getDone() == 0) {
                        $Feuille = $Rapport->getFeuilleDePresence();
                        if ($Feuille->getStatutValidation() == 1) {
                            $Rapport->setConfirmationDeInterventionParClient(true);
                            $em->persist($Rapport);
                            $em->flush();
                            $messageRedaction = (new \Swift_Message('Intervention ' . $Feuille->getClient()->getRaisonSociale() . ' du ' . $Feuille->getDateIntervention()->format("d/m/Y") . ' assuré par ' . $Feuille->getIntervenant()->getNomUtilisateur() . ' ' . $Feuille->getIntervenant()->getPrenomUtilisateur()))
                                ->setFrom(['timplan@timsoft.net' => "Administrateur TimSoft"])
                                ->setBody(
                                    $this->renderView(
                                        '@TimSoftFeuilleRapportIntervention/Emails/validationFeuille.txt.twig', array('Feuille' => $Feuille)
                                    ), 'text/html');
                            $failedRecipients = [];
                            $numSent = 0;
                            $messageRedaction->addTo($Feuille->getIntervenant()->getEmail(), $Feuille->getIntervenant()->getNomUtilisateur() . ' ' . $Feuille->getIntervenant()->getPrenomUtilisateur());
                            $messageRedaction->addCc('w.benmustapha@timsoft.com.tn', 'Wafa Benmustapha');
                            $messageRedaction->addCc('f.cherif@timsoft.com.tn', 'Fatma CHERIF');
                            foreach ($Rapport->getFeuilleDePresence()->getParticipants() as $Participant) {
                                $messageRedaction->addTo($Participant->getAdresseMailPArticipant(), $Participant->getNomParticipant() . ' ' . $Participant->getPrenomParticipant());
                            }
                            $buManager = $Feuille->getIntervention()->getLc()->getCommande()->getBuManager();
                            if ($buManager) {
                                $messageRedaction->addCc($buManager->getEmail(), $buManager->getPrenomUtilisateur() . ' ' . $buManager->getNomUtilisateur());
                            }

                            $messageRedaction->attach(\Swift_Attachment::fromPath($directory));
                            $messageRedaction->attach(\Swift_Attachment::fromPath($directoryF));
                            $numSent += $this->get('mailer')->send($messageRedaction, $failedRecipients);
                            $notificationUser = new NotificationUtilisateur();
                            $notificationUser
                                ->setNotification($validation)
                                ->setUtilisateur($Feuille->getIntervention()->getConfirmePar())
                                ->setVu(0);
                            $em->persist($notificationUser);
                            $em->flush();
                            $pusher = $this->get('mrad.pusher.notificaitons'); // Appel le service
                            $pusher->trigger($notificationUser);

                        } else {
                            $redaction = new Notification();
                            $redaction
                                ->setTitle("Rapport d'intervention en attente de validation")
                                ->setDescription('Client: ' . $Rapport->getFeuilleDePresence()->getClient() . '- Intervenant: ' . $Rapport->getFeuilleDePresence()->getIntervenant())
                                ->setResponsable($this->getUser())
                                ->setRoute('ConsulterRapportIntervention')
                                ->setParameters(array('id' => $Rapport->getId()));
                            $em->persist($redaction);
                            $em->flush();
                            $redactionRapport = new NotificationUtilisateur();
                            $redactionRapport
                                ->setNotification($redaction)
                                ->setUtilisateur($Rapport->getFeuilleDePresence()->getIntervention()->getConfirmePar())
                                ->setVu(0);
                            $em->persist($redactionRapport);
                            $em->flush();
                            $pusher = $this->get('mrad.pusher.notificaitons'); // Appel le service
                            $pusher->trigger($redactionRapport);
                            $redaction = (new \Swift_Message('Redaction d\'un rapport d\'intervention'))
                                ->setFrom(['timplan@timsoft.net' => "Administrateur TimSoft"])
                                ->setBody(
                                    $this->renderView(
                                        '@TimSoftFeuilleRapportIntervention/Emails/redactionRapport.txt.twig', array('Rapport' => $Rapport)
                                    ), 'text/html');
                            $failedRecipients = [];
                            $numSent = 0;
                        }
//                        $redaction->attach(\Swift_Attachment::fromPath($directory));
                        $ChefClient = $Rapport->getFeuilleDePresence()->getIntervention()->getConfirmePar();
//                        $redaction->setTo([$ChefClient->getEmail() => $ChefClient->getNomUtilisateur() . ' ' . $ChefClient->getPrenomUtilisateur()]);
//                        $buManagers = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Utilisateur')->findByRoleUtilisateur('ROLE_CHEF');
//                        foreach ($buManagers as $buManager) {
//                            $bu = $this->getDoctrine()->getRepository('TimSoftBuBundle:Bu')->findOneBy(array('libelle' => $bu));
//                            foreach ($buManager->getBus() as $bus) {
//                                if ($bu == $bus) {
//                                    $redaction->addTo($buManager->getEmail(), $buManager->getNomUtilisateur() . ' ' . $buManager->getPrenomUtilisateur());
//                                }
//                            }
//                        }
//                        $numSent += $this->get('mailer')->send($redaction, $failedRecipients);
                        $Rapport->setDone(true);
                        $em->persist($Rapport);
                        $em->flush();
                        if ($this->getUser()->hasRole('ROLE_CONSULTANT')) {
                            return $this->redirectToRoute('ConsulterFeuillesRapportInterventionParConsultant');
                        } else {
                            return $this->redirectToRoute('ConsulterFeuillesRapportIntervention');
                        }
                    } else {
                        if ($this->getUser()->hasRole('ROLE_CONSULTANT') || $this->getUser()->hasRole('ROLE_GESTIONNAIRE') || $this->getUser()->hasRole('ROLE_CHEF') || $this->getUser()->hasRole('ROLE_ADMIN')) {
                            $Rapport->setConfirmationDeInterventionParClient(true);
//                        var_dump($Rapport);
//                        die();
                        }
                        $notification = new Notification();
                        $notification
                            ->setTitle('Modification d\'un rapport')
                            ->setDescription('Client: ' . $Rapport->getFeuilleDePresence()->getClient() . '- Intervenant: ' . $Rapport->getFeuilleDePresence()->getIntervenant())
                            ->setResponsable($this->getUser())
                            ->setRoute('ConsulterRapportIntervention')
                            ->setParameters(array('id' => $Rapport->getId()));
                        $em->persist($notification);
                        $em->flush();
                        if ($form["confirmationDeInterventionParClient"]->getData() == 1) {
                            $Client = $Feuille->getIntervention()->getConfirmePar();
                            $messageValidation = (new \Swift_Message('Intervention validé : ' . $Feuille->getClient()->getRaisonSociale() . ' du ' . $Feuille->getDateIntervention()->format("d/m/Y") . ' assuré par ' . $Feuille->getIntervenant()->getNomUtilisateur() . ' ' . $Feuille->getIntervenant()->getPrenomUtilisateur()))
                                ->setFrom(['timplan@timsoft.net' => "Administrateur TimSoft"])
                                ->setBody(
                                    $this->renderView(
                                        '@TimSoftFeuilleRapportIntervention/Emails/validationFeuille.txt.twig', array('Feuille' => $Feuille)
                                    ), 'text/html');
                            $failedRecipients = [];
                            $numSent = 0;
                            $messageValidation->setTo([$Client->getEmail() => $Client->getNomUtilisateur() . ' ' . $Client->getPrenomUtilisateur(), $Feuille->getIntervenant()->getEmail() => $Feuille->getIntervenant()->getNomUtilisateur() . ' ' . $Feuille->getIntervenant()->getPrenomUtilisateur()]);
                            $gestionnaires = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Utilisateur')->findByRoleUtilisateur('ROLE_GESTIONNAIRE');
                            foreach ($gestionnaires as $gestionnaire) {
                                $messageValidation->addTo($gestionnaire->getEmail(), $gestionnaire->getNomUtilisateur() . ' ' . $gestionnaire->getPrenomUtilisateur());
                            }
//                            $buManagers = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Utilisateur')->findByRoleUtilisateur('ROLE_CHEF');
//                            foreach ($buManagers as $buManager) {
//                                $bu = $this->getDoctrine()->getRepository('TimSoftBuBundle:Bu')->findOneBy(array('libelle' => $bu));
//                                foreach ($buManager->getBus() as $bus) {
//                                    if ($bu == $bus) {
//                                        $messageValidation->addTo($buManager->getEmail(), $buManager->getNomUtilisateur() . ' ' . $buManager->getPrenomUtilisateur());
//                                    }
//                                }
//                            }
//                            $messageValidation->attach(\Swift_Attachment::fromPath($directory));
//                            $messageValidation = (new \Swift_Message('Validation d\'un rapport d\'intervention'))
//                                ->setFrom(['timplan@timsoft.com.tn' => "Administrateur TimSoft"])
//                                ->setBody(
//                                    $this->renderView(
//                                        '@TimSoftFeuilleRapportIntervention/Emails/validationRapport.txt.twig', array('Rapport' => $Rapport)
//                                    ), 'text/html');
//                            $failedRecipients = [];
//                            $numSent = 0;
                            if ($Rapport->getFeuilleDePresence()->getParticipants() != null) {
                                foreach ($Rapport->getFeuilleDePresence()->getParticipants() as $Participant) {
                                    $messageValidation->addTo($Participant->getAdresseMailPArticipant(), $Participant->getNomParticipant() . ' ' . $Participant->getPrenomParticipant());
                                    $user = $em->getRepository('TimSoftGeneralBundle:Utilisateur')->getUserByEmail($Participant->getAdresseMailPArticipant());
                                    if ($user) {
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
                                }
                                $Admins = $em->getRepository('TimSoftGeneralBundle:Utilisateur')->findBy(array('roleUtilisateur' => "ROLE_ADMIN"));
                                foreach ($Admins as $user) {
                                    //  $message->setTo([$Participant->getAdresseMailParticipant() => $user->getNomUtilisateur() . ' ' . $user->getPrenomUtilisateur()]);
                                    $notificationUser = new NotificationUtilisateur();
                                    $notificationUser
                                        ->setNotification($validation)
                                        ->setUtilisateur($user)
                                        ->setVu(0);
                                    $em->persist($notificationUser);
                                    $em->flush();
                                    $pusher = $this->get('mrad.pusher.notificaitons'); // Appel le service
                                    $pusher->trigger($notificationUser);
                                }
                                $Consultant = $Rapport->getFeuilleDePresence()->getIntervenant();
                                $messageValidation->addTo($Consultant->getEmail(), $Consultant->getNomUtilisateur() . ' ' . $Consultant->getPrenomUtilisateur());
                                foreach ($Rapport->getFichiersJoin() as $Fichier) {
                                    if ($Fichier->getSendParMail()) {
                                        $message->attach(\Swift_Attachment::fromPath($this->getParameter('DossierFichiersJoin') . '/' . $Fichier->getContenuFichierJoin()));
                                    }
                                }
                                $messageValidation->attach(\Swift_Attachment::fromPath($directory));
                                $messageValidation->attach(\Swift_Attachment::fromPath($directoryF));
//

                            }
                            $numSent += $this->get('mailer')->send($messageValidation, $failedRecipients);
                            if ($this->getUser()->hasRole('ROLE_CONSULTANT')) {
                                return $this->redirectToRoute('ConsulterFeuillesRapportInterventionParConsultant');
                            } else {
                                return $this->redirectToRoute('ConsulterFeuillesRapportIntervention');
                            }
                        } else {
                            if ($this->getUser()->hasRole('ROLE_CLIENT')) {
                                $this->addFlash('RapportNV', 'Vous devez validé le rapport !');
                                return $this->render('@TimSoftFeuilleRapportIntervention/GestionFeuilleRapportIntervention/ModifierRapportIntervention.html.twig', array('form1' => $form->createView(), 'Rapport' => $Rapport));
                            } elseif ($this->getUser()->hasRole('ROLE_CONSULTANT')) {
                                return $this->redirectToRoute('ConsulterFeuillesRapportInterventionParConsultant');
                            } else {
                                return $this->redirectToRoute('ConsulterFeuillesRapportIntervention');

                            }
                        }

                        $Admins = $em->getRepository('TimSoftGeneralBundle:Utilisateur')->findBy(array('roleUtilisateur' => "ROLE_ADMIN"));
                        foreach ($Admins as $user) {
                            $notificationUser = new NotificationUtilisateur();
                            $notificationUser
                                ->setNotification($notification)
                                ->setUtilisateur($user)
                                ->setVu(0);
                            $em->persist($notificationUser);
                            $em->flush();
                            $pusher = $this->get('mrad.pusher.notificaitons'); // 3aiet lel service
                            $pusher->trigger($notificationUser);
                        }
                        $Client = $Rapport->getFeuilleDePresence()->getIntervention()->getConfirmePar();
                        //Envoi d'un Mail au participant
                        //   $message->setTo([$Client->getEmail() => $Client->getNomUtilisateur() . ' ' . $Client->getPrenomUtilisateur()]);
//                $Participants[] = $Participant->getAdresseMailParticipant();
//                        $message->attach(\Swift_Attachment::fromPath($directory));
//                        $numSent += $this->get('mailer')->send($message, $failedRecipients);

                        //Chercher si le participant est un Utilisateur et si c'est le cas lui envoyer une notification
                        $user = $em->getRepository('TimSoftGeneralBundle:Utilisateur')->getUserByEmail($Client->getEmail());
                        if ($user) {
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
                        if ($this->getUser() != $Rapport->getFeuilleDePresence()->getIntervenant()) {
                            $notificationUser = new NotificationUtilisateur();
                            $notificationUser
                                ->setNotification($notification)
                                ->setUtilisateur($Rapport->getFeuilleDePresence()->getIntervenant())
                                ->setVu(0);
                            $em->persist($notificationUser);
                            $em->flush();
                            $pusher = $this->get('mrad.pusher.notificaitons'); // Appel le service
                            $pusher->trigger($notificationUser);
                        }
                        $em->persist($Rapport);
                        $em->flush();
                        $this->addFlash('OK-Modification-Rapport', 'Le rapport a été mis à jour avec succès !');
                        return $this->redirectToRoute('ConsulterRapportParClient', array('id' => $Rapport->getFeuilleDePresence()->getId()));

                    }
                }
                return $this->render('@TimSoftFeuilleRapportIntervention/GestionFeuilleRapportIntervention/ModifierRapportIntervention.html.twig', array('form1' => $form->createView(), 'Rapport' => $Rapport));
//            } else {
//                $this->addFlash('KO-ConsulterUtilisateur', 'Vous n\'êtes pas autorisé à accéder a cette ressource, Vous serez redirigé vers la page d\'accueil !');
//                return $this->redirectToRoute('tim_soft_client_homepage');
//            }
            } else {
                $this->addFlash('RapportValide', 'Le rapport est déjà validé, vous ne pouvez plus le modifier !');
                return $this->redirectToRoute('ConsulterFeuillesRapportIntervention');
            }
        } else {
            $this->addFlash('FeuilleNonValide', 'Vous devez valider la feuille de présence avant la validation du rapport d\'intervention !');
            return $this->redirectToRoute('ModifierFeuillePresence', array('id' => $Rapport->getFeuilleDePresence()->getId()));
        }
    }

    /**
     * @Route("/TelechargementFeuillePresence/{id}", name="TelechargerFeuillePresence", options = { "expose" = true },)
     */
    public
    function telechargerFeuillePresenceAction($id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Feuille = $em->getRepository("TimSoftGeneralBundle:FeuilleDePresence")->findOneBy(array('id' => $id));

        $view = $this->renderView('@TimSoftFeuilleRapportIntervention/Telechargement/TelechargerUneFeuillePresence.html.twig', array('Feuille' => $Feuille));
// On crée une  instance pour définir les options de notre fichier pdf
        $options = new Options();
        // Pour simplifier l'affichage des images, on autorise dompdf à utiliser
        // des  url pour les nom de  fichier
        $options->set('isRemoteEnabled', true);
        $d = new Dompdf($options);
        $contxt = stream_context_create([
            'ssl' => [
                'verify_peer' => FALSE,
                'verify_peer_name' => FALSE,
                'allow_self_signed' => TRUE
            ]
        ]);
        $d->setHttpContext($contxt);
        $d->loadHtml($view);

        // Render the HTML as PDF
        $d->render();

        $fileName = 'Feuille de présence du ' . $Feuille->getDateIntervention()->format('d-m-Y') . ' par ' . $Feuille->getIntervenant() . ' pour  ' . $Feuille->getClient();
        $fileName = str_replace('/', '', $fileName);
//        die(var_dump($fileName));
        //$d->stream($fileName);
//        $directory = $this->get('kernel')->getRootDir() . '/../web/PDF/'.$fileName.'.pdf';
//        $pdf_string =   $d->output();
//        file_put_contents($directory, $pdf_string ); 
        //return $directory;
//        $directory = $this->get('kernel')->getRootDir() . '/../web/PDF/';
//        $d->stream($fileName)->move($directory, $fileName);
        $d->stream($fileName, array(
            'Attachment' => true,
        ));

        return $this->render('@TimSoftFeuilleRapportIntervention/Telechargement/TelechargerUneFeuillePresence.html.twig', array('Feuille' => $Feuille));
//            return new Response('', 200, array('Content-Type' => 'application/pdf'));


        // return $this->render('@TimSoftFeuilleRapportIntervention/GestionFeuilleRapportIntervention/ConsulterUneFeuillePresence.html.twig', array('Feuille' => $Feuille));
    }

    /**
     * @Route("/TelechargementRapportIntervention/{id}", name="TelechargerRapportIntervention", options = { "expose" = true },)
     */
    public
    function telechargerRapportInterventionAction($id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Rapport = $em->getRepository("TimSoftGeneralBundle:RapportIntervention")->findOneBy(array('id' => $id));

        $view = $this->renderView('@TimSoftFeuilleRapportIntervention/Telechargement/TelechargerUnRapport.html.twig', array('Rapport' => $Rapport));
// On crée une  instance pour définir les options de notre fichier pdf
        $options = new Options();
        // Pour simplifier l'affichage des images, on autorise dompdf à utiliser
        // des  url pour les nom de  fichier
        $options->set('isRemoteEnabled', true);
        $d = new Dompdf($options);
        $contxt = stream_context_create([
            'ssl' => [
                'verify_peer' => FALSE,
                'verify_peer_name' => FALSE,
                'allow_self_signed' => TRUE
            ]
        ]);
        $d->setHttpContext($contxt);
        $d->loadHtml($view);

        // Render the HTML as PDF
        $d->render();

        $fileName = 'Rapport de l\'intervention du ' . $Rapport->getFeuilleDePresence()->getDateIntervention()->format('d-m-Y') . ' par ' . $Rapport->getFeuilleDePresence()->getIntervenant() . ' ' . $Rapport->getFeuilleDePresence()->getClient();
        $fileName = str_replace('/', '', $fileName);
//        die(var_dump($fileName));
        $d->stream($fileName, array(
            'Attachment' => 1,
        ));

        return $this->render('@TimSoftFeuilleRapportIntervention/Telechargement/TelechargerUnRapport.html.twig', array('Rapport' => $Rapport));
//            return new Response('', 200, array('Content-Type' => 'application/pdf'));


//             return $this->render('@TimSoftFeuilleRapportIntervention/GestionFeuilleRapportIntervention/ConsulterUneFeuillePresence.html.twig', array('Feuille' => $Feuille));
    }

    /**
     * @Route("/ConsulterRapportPartType/{typeIntervention}", name="ConsulterRapportPartType")
     */
    public
    function consulterRapportParTypeAction($typeIntervention)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Rapports = $em->getRepository('TimSoftGeneralBundle:RapportIntervention')->getRapportParType($typeIntervention);
        return $this->render('@TimSoftFeuilleRapportIntervention/GestionFeuilleRapportIntervention/ConsulterFeuilleRapportIntervention.html.twig', array('Rapports' => $Rapports));
    }

    /**
     * @Route("/ConsulterRapport", name="ConsulterTousLesRapport")
     */
    public
    function consulterRapportAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Rapports = $em->getRepository('TimSoftGeneralBundle:RapportIntervention')->findAll();
        return $this->render('@TimSoftFeuilleRapportIntervention/GestionFeuilleRapportIntervention/ConsulterFeuilleRapportIntervention.html.twig', array('Rapports' => $Rapports));
    }

    /**
     * @Route("/ConsulterRapportParValidation/{validation}", name="ConsulterRapportParValidation")
     */
    public
    function consulterRapportParValidationAction($validation)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Rapports = $em->getRepository('TimSoftGeneralBundle:RapportIntervention')->getRapportParValidation($validation);
        return $this->render('@TimSoftFeuilleRapportIntervention/GestionFeuilleRapportIntervention/ConsulterFeuilleRapportIntervention.html.twig', array('Rapports' => $Rapports));
    }

    /**
     * @Route("/ConsulterFeuilleParValidation/{validation}", name="ConsulterFeuilleParValidation")
     */
    public
    function consulterFeuilleParValidationAction($validation)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Feuilles = $em->getRepository('TimSoftGeneralBundle:FeuilleDePresence')->getFeuilleParValidation($validation);
        return $this->render('@TimSoftFeuilleRapportIntervention/GestionFeuilleRapportIntervention/ConsulterFeuilleRapportIntervention.html.twig', array('Feuilles' => $Feuilles));
    }

    /**
     * @Route("/ConsulterFeuilles", name="ConsulterToutesLesFeuilles")
     */
    public
    function consulterFeuillesAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Feuilles = $em->getRepository('TimSoftGeneralBundle:FeuilleDePresence')->findAll();
        return $this->render('@TimSoftFeuilleRapportIntervention/GestionFeuilleRapportIntervention/ConsulterFeuilleRapportIntervention.html.twig', array('Feuilles' => $Feuilles));
    }

    /**
     * @Route("/ConsulterFeuillesClient", name="ConsulteFeuillesClient")
     */
    public
    function consulterFeuillesClientAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Feuilles = $em->getRepository('TimSoftGeneralBundle:FeuilleDePresence')->getFeuilleParClient($this->getUser()->getClient());
        $Rapports = $em->getRepository('TimSoftGeneralBundle:RapportIntervention')->getRapportClient($this->getUser()->getClient());
        return $this->render('@TimSoftFeuilleRapportIntervention/GestionFeuilleRapportIntervention/ConsulterFeuilleRapportIntervention.html.twig', array('Feuilles' => $Feuilles, 'Rapport' => $Rapports));
    }

    /**
     * @Route("/ConsulterFeuilleParValidationParClient/{validation}", name="ConsulterFeuilleParValidationParClient")
     */
    public
    function consulterFeuilleParValidationParClientAction($validation)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Feuilles = $em->getRepository('TimSoftGeneralBundle:FeuilleDePresence')->getFeuilleParValidationParClient($validation, $this->getUser()->getClient());
        return $this->render('@TimSoftFeuilleRapportIntervention/GestionFeuilleRapportIntervention/ConsulterFeuilleRapportIntervention.html.twig', array('Feuilles' => $Feuilles));
    }

    /**
     * @Route("/ConsulterFeuilleParConsultant", name="ConsulterFeuilleParConsultant")
     */
    public
    function consulterFeuilleParConsultantAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Feuilles = $em->getRepository('TimSoftGeneralBundle:FeuilleDePresence')->getFeuilleParConsultant($this->getUser()->getId());
        return $this->render('@TimSoftFeuilleRapportIntervention/GestionFeuilleRapportIntervention/ConsulterFeuilleRapportIntervention.html.twig', array('Feuilles' => $Feuilles));
    }

    /**
     * @Route("/ConsulterFeuilleParStatutParConsultant/{validation}", name="ConsulterFeuilleParStatutParConsultant")
     */
    public
    function consulterFeuilleParStatutParConsultantAction($validation)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Feuilles = $em->getRepository('TimSoftGeneralBundle:FeuilleDePresence')->getFeuilleParStatutParConsultant($this->getUser()->getId(), $validation);
        return $this->render('@TimSoftFeuilleRapportIntervention/GestionFeuilleRapportIntervention/ConsulterFeuilleRapportIntervention.html.twig', array('Feuilles' => $Feuilles));
    }

    /**
     * @Route("/ConsulterRapportParValidationParConsultant/{validation}", name="ConsulterRapportParValidationParConsultant")
     */
    public
    function consulterRapportParValidationParConsultantAction($validation)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Rapports = $em->getRepository('TimSoftGeneralBundle:RapportIntervention')->getRapportParValidationParConsultant($this->getUser()->getId(), $validation);
        return $this->render('@TimSoftFeuilleRapportIntervention/GestionFeuilleRapportIntervention/ConsulterFeuilleRapportIntervention.html.twig', array('Rapports' => $Rapports));
    }

    /**
     * @Route("/ConsulterRapportParConsultant", name="ConsulterRapportParConsultant")
     */
    public
    function consulterRapportParConsultantAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Rapports = $em->getRepository('TimSoftGeneralBundle:RapportIntervention')->getRapportConsultant($this->getUser()->getId());
        return $this->render('@TimSoftFeuilleRapportIntervention/GestionFeuilleRapportIntervention/ConsulterFeuilleRapportIntervention.html.twig', array('Rapports' => $Rapports));
    }

    /**
     * @Route("/ConsulterRapportParClient", name="ConsulterRapportParClient")
     */
    public
    function consulterRapportParClienttAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Rapports = $em->getRepository('TimSoftGeneralBundle:RapportIntervention')->getRapportClient($this->getUser()->getClient());
        return $this->render('@TimSoftFeuilleRapportIntervention/GestionFeuilleRapportIntervention/ConsulterFeuilleRapportIntervention.html.twig', array('Rapports' => $Rapports));
    }

    /**
     * @Route("/ConsulterRapportParValidationParClient/{validation}", name="ConsulterRapportParValidationParClient")
     */
    public
    function consulterRapportParValidationParClienttAction($validation)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Rapports = $em->getRepository('TimSoftGeneralBundle:RapportIntervention')->getRapportParValidationParClient($validation, $this->getUser()->getClient());
        return $this->render('@TimSoftFeuilleRapportIntervention/GestionFeuilleRapportIntervention/ConsulterFeuilleRapportIntervention.html.twig', array('Rapports' => $Rapports));
    }

    /**
     * @Route("/ConsulterRapportPartTypeParClient/{typeIntervention}", name="ConsulterRapportPartTypeParClient")
     */
    public
    function consulterRapportParTypeParClientAction($typeIntervention)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Rapports = $em->getRepository('TimSoftGeneralBundle:RapportIntervention')->getRapportParTypeParClient($typeIntervention, $this->getUser()->getClient());
        return $this->render('@TimSoftFeuilleRapportIntervention/GestionFeuilleRapportIntervention/ConsulterFeuilleRapportIntervention.html.twig', array('Rapports' => $Rapports));
    }

    /**
     * @Route("/ConsulterRapportPartTypeParConsultant/{typeIntervention}", name="ConsulterRapportPartTypeParConsultant")
     */
    public
    function consulterRapportParTypeParConsultantAction($typeIntervention)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Rapports = $em->getRepository('TimSoftGeneralBundle:RapportIntervention')->getRapportParTypeParConsultant($typeIntervention, $this->getUser()->getId());
        return $this->render('@TimSoftFeuilleRapportIntervention/GestionFeuilleRapportIntervention/ConsulterFeuilleRapportIntervention.html.twig', array('Rapports' => $Rapports));
    }

    function clean($string)
    {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }

    /**
     *
     * @Route("/rapprochement", name="rapprochement")
     */
    public function rapprochementAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Feuilles = $em->getRepository('TimSoftGeneralBundle:FeuilleDePresence')->findAll();
        return $this->render('@TimSoftFeuilleRapportIntervention/GestionFeuilleRapportIntervention/Rapprochement.html.twig', array('Feuilles' => $Feuilles));
    }

    /**
     * @param Request $request
     * @Route("/rapprochement/ajout", name="rapprochementAjout")
     * @return JsonResponse
     */
    public function rapprochementPostAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Feuille = $em->getRepository('TimSoftGeneralBundle:FeuilleDePresence')->find($request->get('pk'));
        $Feuilles = $em->getRepository('TimSoftGeneralBundle:FeuilleDePresence')->findBy(array('numLivraison' => $request->get('value')));

        $num = $request->get('value');
        $Feuille->setNumLivraison($num);
        $em->persist($Feuille);

        if ($Feuilles) {
            return new JsonResponse('NumExist', 200);
        } else {
            $em->flush();
            return new JsonResponse($Feuille, 200);
        }
    }

    public function valider($Feuille)
    {
        $em = $this->getDoctrine()->getManager();
        $Feuille->getIntervention()->setStatut('Terminé');
        $em->persist($Feuille->getIntervention());
        $ligneCommande = $Feuille->getIntervention()->getLc();
        if ($Feuille->getIntervention()->getJSupplementaire() == 0) {
            $nb = $ligneCommande->getQteRestante() - $Feuille->getIntervention()->jRestantes();
            $ligneCommande->setQteRestante($nb);
        }
        $Feuille->setStatutValidation(true);
        $em->persist($ligneCommande);
        $em->persist($Feuille);
        $em->flush();
    }

    /**
     *
     * @Route("/feuillesrapports/list", name="feuillesrapportsJson")
     * @Method({"GET", "POST"})
     */
    public function feuillesrapportsJsonAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if ($request->get('parClient') == 'parClient') {
            $Feuilles = $em->getRepository('TimSoftGeneralBundle:FeuilleDePresence')->findBy(array('client' => $this->getUser()->getClient()), array('dateIntervention' => 'DESC'));
//            $Rapports = $em->getRepository('TimSoftGeneralBundle:RapportIntervention')->getRapportClient($this->getUser()->getClient());
        } elseif ($request->get('ParConsultant') == 'ParConsultant') {
            $Feuilles = $em->getRepository('TimSoftGeneralBundle:FeuilleDePresence')->findBy(array('intervenant' => $this->getUser()), array('dateIntervention' => 'DESC'));
        } else {
            $Feuilles = $em->getRepository('TimSoftGeneralBundle:FeuilleDePresence')->findBy(array(), array('dateIntervention' => 'DESC'));
        }
        $json = [];
        foreach ($Feuilles as $feuille) {
            $json[] = $feuille;
            if ($feuille->getRapportIntervention()) {
                $json[] = $feuille->getRapportIntervention();
            }
        }
        return new JsonResponse($json);
    }

    /**
     *
     * @Route("/feuilles/list", name="feuillesJson")
     * @Method({"GET", "POST"})
     */
    public function feuillesJsonAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $Feuilles = $em->getRepository('TimSoftGeneralBundle:FeuilleDePresence')->findBy(array(), array('dateIntervention' => 'DESC'));
        $json = [];
        foreach ($Feuilles as $feuille) {
            $json[] = $feuille;
        }
        return new JsonResponse($json);
    }
}
