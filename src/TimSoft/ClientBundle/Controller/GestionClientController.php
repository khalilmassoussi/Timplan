<?php

namespace TimSoft\ClientBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use TimSoft\GeneralBundle\Entity\Client;
use Symfony\Component\Form\FormView;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use TimSoft\GeneralBundle\Form\ImporterClientType;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class GestionClientController extends Controller
{

    //Methode permettant d'ajouter un nouveau client
    public function ajouterClientAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $Client = new Client();
        $form = $this->createForm('TimSoft\GeneralBundle\Form\ClientType', $Client);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $Client = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $ClientAvecMemeCodeClient = $em->getRepository('TimSoftGeneralBundle:Client')->findOneBy(array('codeClient' => $Client->getCodeClient()));
            $ClientAvecMemeRaisonSociale = $em->getRepository('TimSoftGeneralBundle:Client')->findOneBy(array('raisonSociale' => $Client->getRaisonSociale()));
            $ClientAvecMemeEmail = $em->getRepository('TimSoftGeneralBundle:Client')->findOneBy(array('adresseMailClient' => $Client->getAdresseMailClient()));

            if (!empty($ClientAvecMemeCodeClient)) {
                $form->get('codeClient')->addError(new FormError('Le code client est déjà utilisé'));
            }
            if (!empty($ClientAvecMemeRaisonSociale)) {
                $form->get('raisonSociale')->addError(new FormError('La raison sociale est déjà utilisée'));
            }
            if (!empty($ClientAvecMemeEmail)) {
                $form->get('adresseMailClient')->addError(new FormError('L\'adresse Mail est déjà utilisée'));
            }
            if (empty($ClientAvecMemeCodeClient) && empty($ClientAvecMemeRaisonSociale) && empty($ClientAvecMemeEmail)) {
                foreach ($Client->getClientFacture() as $child) {
                    $child->setSocieteMere($Client);
                }
                $em->persist($Client); //L'enregistrement se fait uniquement dans Doctrine
                $em->flush(); // pour sauvegarder les données dans la BD
                $this->addFlash('OK-Ajout-Client', 'Le client ' . $Client->getRaisonSociale() . ' est ajouté avec succès !');
                return $this->redirectToRoute('ConsulterUnClient', array('id' => $Client->getId()));
            }
        }
        return $this->render('@TimSoftClient/GestionClient/AjouterClient.html.twig', array('form' => $form->createView()));
    }

    //Methode qui affiche tous les client
    public function consulterClientAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Clients = $em->getRepository('TimSoftGeneralBundle:Client')->getClients();

        return $this->render('@TimSoftClient/GestionClient/ConsulterClient.html.twig', array('clients' => $Clients));
    }

    //Methode permettant de visualiser les information cancernant un utilisateur specifique
    public function consulterUnClientAction($id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Client = $em->getRepository("TimSoftGeneralBundle:Client")->findOneBy(array('id' => $id));
        $Utilisateurs = $em->getRepository("TimSoftGeneralBundle:Utilisateur")->findBy(array('client' => $id));
        $Factures = $em->getRepository("TimSoftGeneralBundle:Facture")->findBy(array('client' => $id));
        $Feuilles = $em->getRepository("TimSoftGeneralBundle:FeuilleDePresence")->findBy(array('client' => $id));
        return $this->render('@TimSoftClient/GestionClient/ConsulterUnClient.html.twig', array('client' => $Client, 'utilisateurs' => $Utilisateurs, 'Factures' => $Factures, 'Feuilles' => $Feuilles));
    }

    //Methode Permettant d'afficher les information d'un client avant de le supprimer
    public function supprimerUnClientAction($id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Client = $em->getRepository("TimSoftGeneralBundle:Client")->findOneBy(array('id' => $id));
        $Clients = $Client->getClientFacture();
        $Utilisateurs = $em->getRepository("TimSoftGeneralBundle:Utilisateur")->findBy(array('client' => $id));
        $Factures = $em->getRepository("TimSoftGeneralBundle:Facture")->findBy(array('client' => $id));
        $Feuilles = $em->getRepository("TimSoftGeneralBundle:FeuilleDePresence")->findBy(array('client' => $id));


        $TousLesClientsASupprimer = $Clients;
        $TousLesUserASupprimer = $Utilisateurs;
        $TousLesFacturesASupprimer = $Factures;
        $TousLesFeuillesASupprimer = $Feuilles;
        $TousLesRapportsASupprimer = Array();

        foreach ($Feuilles as $Feuille) {
            $RapportRalatif = $em->getRepository("TimSoftGeneralBundle:RapportIntervention")->findOneBy(array('FeuilleDePresence' => $Feuille->getId()));
            if ($RapportRalatif != null) {
                $TousLesRapportsASupprimer[] = $RapportRalatif;
            }
        }
        if ($Clients != null) {

            foreach ($Clients as $Clt) {

                $UsersClt = $em->getRepository("TimSoftGeneralBundle:Utilisateur")->findBy(array('client' => $Clt->getId()));
                foreach ($UsersClt as $userClt) {
                    $TousLesUserASupprimer[] = $userClt;
                }
                $FacturesClt = $em->getRepository("TimSoftGeneralBundle:Facture")->findBy(array('client' => $Clt->getId()));
                foreach ($FacturesClt as $FactureClt) {
                    $TousLesFacturesASupprimer[] = $FactureClt;
                }
                $FeuillesClt = $em->getRepository("TimSoftGeneralBundle:FeuilleDePresence")->findBy(array('client' => $Clt->getId()));
                foreach ($FeuillesClt as $FeuilleClt) {
                    $TousLesFeuillesASupprimer[] = $FeuilleClt;
                    $RapportRalatifClt = $em->getRepository("TimSoftGeneralBundle:RapportIntervention")->findOneBy(array('FeuilleDePresence' => $FeuilleClt->getId()));
                    if ($RapportRalatifClt != null) {
                        $TousLesRapportsASupprimer[] = $RapportRalatifClt;
                    }
                }
            }
        }

        return $this->render('@TimSoftClient/GestionClient/SupprimerUnClient.html.twig', array('client' => $Client, 'ClientsASupprimer' => $TousLesClientsASupprimer, 'UserASupprimer' => $TousLesUserASupprimer, 'FacturesASupprimer' => $TousLesFacturesASupprimer,
            'FeuillesASupprimer' => $TousLesFeuillesASupprimer, 'RapportsASupprimer' => $TousLesRapportsASupprimer));
    }

    //Methode qui supprime carement le client
    public function supprimerClientAction($id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Client = $em->getRepository("TimSoftGeneralBundle:Client")->findOneBy(array('id' => $id));
        $em->remove($Client);
        $em->flush();
        $this->addFlash('OK-Suppression-Client', 'Le client ' . $Client->getRaisonSociale() . ' a été supprimé avec succès !');
        return $this->redirectToRoute('ConsulterClient');
    }

    //Methode Permettant de modifier des informations relaives au client
    public function modifierClientAction($id, Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Client = $em->getRepository("TimSoftGeneralBundle:Client")->findOneBy(array('id' => $id));
        $AncienneListe = $Client->getClientFacture()->toArray();
        $form = $this->createForm('TimSoft\GeneralBundle\Form\ClientType', $Client);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $Client = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $ClientAvecMemeCodeClient = $em->getRepository('TimSoftGeneralBundle:Client')->findOneBy(array('codeClient' => $Client->getCodeClient()));
            $ClientAvecMemeRaisonSociale = $em->getRepository('TimSoftGeneralBundle:Client')->findOneBy(array('raisonSociale' => $Client->getRaisonSociale()));
            $ClientAvecMemeEmail = $em->getRepository('TimSoftGeneralBundle:Client')->findOneBy(array('adresseMailClient' => $Client->getAdresseMailClient()));

//            if (!empty($ClientAvecMemeCodeClient) && ($ClientAvecMemeCodeClient != $Client)) {
//                $form->get('codeClient')->addError(new FormError('Le code client est déjà utilisé'));
//            }
//            if (!empty($ClientAvecMemeRaisonSociale) && ($ClientAvecMemeRaisonSociale != $Client)) {
//                $form->get('raisonSociale')->addError(new FormError('La raison sociale est déjà utilisée'));
//            }
//            if (!empty($ClientAvecMemeEmail) && ($ClientAvecMemeEmail != $Client)) {
//                $form->get('adresseMailClient')->addError(new FormError('L\'adresse Mail est déjà utilisée'));
//            }

            $Condition1 = (empty($ClientAvecMemeCodeClient) || ($ClientAvecMemeCodeClient == $Client));
            $Condition2 = (empty($ClientAvecMemeRaisonSociale) || ($ClientAvecMemeRaisonSociale == $Client));
            $Condition3 = (empty($ClientAvecMemeEmail) || ($ClientAvecMemeEmail == $Client));
            if ($Condition1 && $Condition2 && $Condition3) {
                $currentList = $Client->getClientFacture();
                foreach ($AncienneListe as $Clt) {
                    $Clt->setSocieteMere(null);
                    $em->persist($Clt);
                }

                foreach ($currentList as $f) {
                    $f->setSocieteMere($Client);
                    $em->persist($f);
                }

            }
            $em->persist($Client); //L'enregistrement se fait uniquement dans Doctrine
            $em->flush(); // pour sauvegarder les données dans la BD
            $this->addFlash('OK-Modification-Client', 'Le client ' . $Client->getRaisonSociale() . ' est mis à jour avec succès !');
            return $this->redirectToRoute('ConsulterUnClient', array('id' => $Client->getId()));
        }
        return $this->render('@TimSoftClient/GestionClient/ModifierClient.html.twig', array('form' => $form->createView(), 'client' => $Client));
    }

    //Methode permettant d'importer des client à partir d'un ichier Excel
    public function importerClientAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $Ok = 0;
        $erreur = 0;
        $form = $this->createForm(ImporterClientType::class);
        //$request= $this-> getRequest();
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
//            var_dump($form);
//            die();
            if ($form->isValid()) {
                $file = $form->get('listeClient')->getData();
                $fn = md5(uniqid()) . '_' . $file->getClientOriginalName();
                $directory = $this->get('kernel')->getRootDir() . '/../web/uploadedfiles/';

                $file->move($directory, $fn);
                $phpExcel = $this->get('phpexcel')->createPHPExcelObject($directory . $fn);
                $em = $this->getDoctrine()->getManager();
                var_dump($directory . $fn);
                $highestRow = $phpExcel->setActiveSheetIndex(0)->getHighestRow();
                var_dump($highestRow);

                foreach ($phpExcel->getWorksheetIterator() as $worksheet) {
                    foreach ($worksheet->getRowIterator() as $row) {
                        $Client = new Client();
                        $cellIterator = $row->getCellIterator();
                        $cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set

                        if ($row->getRowIndex() > 1 && $row->getRowIndex() <= $highestRow) {
                            $Client = new Client();
                            foreach ($cellIterator as $cell) {
                                if (!is_null($cell)) {
                                    if ('A' == $cell->getColumn()) {
                                        $CodeClient = trim($cell->getCalculatedValue());
                                        $Client->setCodeClient($CodeClient);
                                    }
                                    if ('B' == $cell->getColumn()) {
                                        $RaisonSociale = trim($cell->getCalculatedValue());
                                        $Client->setRaisonSociale($RaisonSociale);
                                    }
                                    if ('C' == $cell->getColumn()) {
                                        $Telephone = trim($cell->getCalculatedValue());
                                        $Client->setTelephone($Telephone);
                                    }
                                    if ('D' == $cell->getColumn()) {
                                        $Mail = trim($cell->getCalculatedValue());
                                        $Client->setAdresseMailClient($Mail);
                                    }
                                    if ('E' == $cell->getColumn()) {
                                        $CodePostal = trim($cell->getCalculatedValue());
                                        $Client->setCodePostalClient($CodePostal);
                                    }
                                    if ('F' == $cell->getColumn()) {
                                        $Adresse = trim($cell->getCalculatedValue());
                                        $Client->setAdresseClient($Adresse);
                                    }
                                    if ('G' == $cell->getColumn()) {
                                        $Ville = trim($cell->getCalculatedValue());
                                        $Client->setVilleClient($Ville);
                                    }
                                    if ('H' == $cell->getColumn()) {
                                        $Pays = trim($cell->getCalculatedValue());
                                        $Client->setPaysClient($Pays);
                                    }
                                }
                            }

                            $ClientAvecMemeCodeClient = $em->getRepository('TimSoftGeneralBundle:Client')->findOneBy(array('codeClient' => $Client->getCodeClient()));
                            $ClientAvecMemeRaisonSociale = $em->getRepository('TimSoftGeneralBundle:Client')->findOneBy(array('raisonSociale' => $Client->getRaisonSociale()));
                            if ((!empty($ClientAvecMemeCodeClient)) || (!empty($ClientAvecMemeRaisonSociale))) {
                                $erreur += 1;
                            } else {
                                $em->persist($Client);
                                $em->flush();
                                $Ok += 1;
                            }
                        }
                    }
                }
                $this->addFlash('OK-Import-Client', 'L\'import de ' . $Ok . '  client(s) s\'est effectué avec succès! ( ' . $erreur . ' Client(s) dupliqué(s) (avec même raison sociale ou code) ont été ignorés ');
                return $this->redirectToRoute('ConsulterClient');
            }
        }

        return $this->render('@TimSoftClient/GestionClient/ImporterClient.html.twig', array('form' => $form->createView()));
    }



}
