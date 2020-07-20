<?php

namespace TimSoft\CommandeBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TimSoft\CommandeBundle\Entity\LigneCommande;
use TimSoft\CommandeBundle\Entity\PreLigneCommande;
use TimSoft\CommandeBundle\Entity\PreTeteCommande;
use TimSoft\CommandeBundle\Entity\TeteCommande;
use TimSoft\CommandeBundle\Form\ImportCommandeType;
use Symfony\Component\Routing\Annotation\Route;
use TimSoft\CommandeBundle\Form\PreTeteCommandeType;
use TimSoft\CommandeBundle\Form\TeteCommandeType;

class PreCommandeController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     * @Route("/import", name="preCommandeImport")
     */
    public function importerCmdAction(Request $request)
    {

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $duplicate = [];
        function Test($array, $key)
        {
            $temp_array = array();
            $i = 0;
            $key_array = array();

            foreach ($array as $val) {
                if (!in_array($val[$key], $key_array)) {
                    $key_array[$i] = $val[$key];
                    $temp_array[$i] = $val;
                }
                $i++;
            }
            return $temp_array;
        }

        $duplicateEC = [];
        $dup = [];
        $Ok = 0;
        $erreur = 0;
        $form = $this->createForm(ImportCommandeType::class);
        //$request= $this-> getRequest();
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $file = $form->get('listeCmd')->getData();
                $fn = md5(uniqid()) . '_' . $file->getClientOriginalName();
                $directory = $this->get('kernel')->getRootDir() . '/../web/uploadedfiles/';
                $rows = [];
                $fullrows = [];
                $file->move($directory, $fn);
                $phpExcel = $this->get('phpexcel')->createPHPExcelObject($directory . $fn);
                $em = $this->getDoctrine()->getManager();
                $highestRow = $phpExcel->setActiveSheetIndex(0)->getHighestRow();
                foreach ($phpExcel->getWorksheetIterator() as $worksheet) {
                    foreach ($worksheet->getRowIterator() as $row) {
                        $cellIterator = $row->getCellIterator();
                        $cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set
                        $fullcells = [];
                        $cells = [];
                        if ($row->getRowIndex() == 1) {
                            foreach ($cellIterator as $key => $cell) {
                                if (($key == 'A' && $cell->getValue() != 'Marché') || ($key == 'B' && $cell->getValue() != 'Tiers') || ($key == 'C' && $cell->getValue() != 'Numéro')
                                    || ($key == 'D' && $cell->getValue() != 'Num. Ligne') || ($key == 'E' && $cell->getValue() != 'Date') || ($key == 'F' && $cell->getValue() != 'Type article')
                                    || ($key == 'G' && $cell->getValue() != 'Libellé Intervention') || ($key == 'H' && $cell->getValue() != 'Quantité') || ($key == 'I' && $cell->getValue() != 'Montant HT')
                                    || ($key == 'J' && $cell->getValue() != 'Qté restante') || ($key == 'K' && $cell->getValue() != 'Valeur Restante') || ($key == 'L' && $cell->getValue() != 'Business Manager')) {
                                    $this->addFlash("Erreur", "Le format de ce fichier excel est invalide");
                                    return $this->render('@TimSoftCommande/Default/ImportCmd.html.twig', array('form' => $form->createView()));
                                }
                            }
                        }
                        if ($row->getRowIndex() > 1 && $row->getRowIndex() <= $highestRow) {
                            foreach ($cellIterator as $cell) {
                                $fullcells[] = $cell->getValue();
                                if ('E' == $cell->getColumn()) {
//                                    print_r($cell->getValue());
                                    if (is_numeric($cell->getValue())) {
                                        $val = date('Y-m-d', \PHPExcel_Shared_Date::ExcelToPHP($cell->getValue()));
                                        $cells[] = $val;
                                    }
                                }
                                if ('C' == $cell->getColumn() || 'B' == $cell->getColumn() || 'L' == $cell->getColumn()) {
                                    $cells[] = $cell->getValue();
                                }

                            }
                        }
                        $rows[] = $cells;
                        $fullrows[] = $fullcells;

                    }
                }
                $buManager = null;
                foreach ($fullrows as $fullrow) {
                    if ($fullrow) {
                        if ($fullrow[0] != null) {
                            $bu = $em->getRepository('TimSoftBuBundle:Bu')->findOneByLibelle($fullrow[0]);
//                            var_dump($fullrow);
//                    die();
                            if (is_null($bu)) {
                                $this->addFlash("Erreur", "Le BU " . $fullrow[0] . " invalide");
                                return $this->render('@TimSoftCommande/Default/ImportCmd.html.twig', array('form' => $form->createView()));
                            }
                        }
                        $users = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Utilisateur')->findAll();
                        if ($fullrow[11]) {
                            foreach ($users as $user) {
                                if ($user->__toString() == $fullrow[11]) {
                                    $buManager = $user;
//                                    var_dump($buManager);
                                }
                            }
                            if ($buManager == null) {
                                $this->addFlash("Erreur", "Business Manager " . $fullrow[11] . " est invalide");
                                return $this->render('@TimSoftCommande/Default/ImportCmd.html.twig', array('form' => $form->createView()));
                            }
                        }
                    }
                }
//                die();
                $array = new ArrayCollection();
                foreach ($rows as $key => $row) {
                    if ($key) {
                        if (!$array->contains($row))
                            $array->add($row);
                    }
                }
//                print_r($array);
                foreach ($array as $value) {
                    if ($value) {
//                        print_r($value);
//                        die();
                        if ($value[1] && $value[2] && $value[0] && $value[3]) {
                            $commande = new PreTeteCommande();
                            $commande->setNCommande($value[1]);
                            $date = new \DateTime($value[2]);
                            $commande->setDatePiece($date);
                            $commande->setBuManager($buManager);
                            $clinet = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Client')->getByName($value[0]);
                            if ($clinet) {
                                $commande->setClient(array_values($clinet)[0]);
                            } else {
                                $client = new \TimSoft\GeneralBundle\Entity\Client();
                                $client->setRaisonSociale($value[0]);
                                $em->persist($client);
                                $em->flush();
                                $commande->setClient($client);
                            }
                            $existe = $em->getRepository('TimSoftCommandeBundle:PreTeteCommande')->findBy(array('nCommande' => $commande->getNCommande()));
                            if (!$existe) {
                                $em->persist($commande);
                            }
                        }
                    }
                }
                $em->flush();
                $lignes = [];
                foreach ($fullrows as $fullrow) {
                    $ligneCommande = new PreLigneCommande();
                    if ($fullrow) {
//                        var_dump($fullrow[2]);
//                        die();
                        $c = $this->getDoctrine()->getRepository('TimSoftCommandeBundle:PreTeteCommande')->getByNumber($fullrow[2]);
                        $lc = $this->getDoctrine()->getRepository('TimSoftCommandeBundle:PreLigneCommande')->findCmd($c, $fullrow[3]);
                        if ($c && !$lc) {
                            if ($fullrow[3] && $fullrow[7]) {
                                $ligneCommande->setNLigne($fullrow[3]);
                                $ligneCommande->setType($fullrow[5]);
                                $ligneCommande->setBu($fullrow[0]);
                                $ligneCommande->setCommande($c);
                                $ligneCommande->setQuantite($fullrow[7]);
                                $ligneCommande->setLibelle($fullrow[6]);
                                $ligneCommande->setMontantHT($fullrow[8]);
                                $ligneCommande->setQteRestante($fullrow[9]);
                                $em->persist($ligneCommande);
                                $em->flush();
                                $lignes[] = $ligneCommande;

                            }
                        } else {
                            if ($lc) {
                                if (!$c->getClient() || (trim($c->getClient()->getRaisonSociale()) != trim($fullrow[1]))) {
                                    $newEntete = new PreTeteCommande();
                                    $newEntete->setNCommande($c->getNCommande());
                                    $newEntete->setBuManager($buManager);
                                    $clinet = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Client')->getByName(trim($fullrow[1]));
                                    if ($clinet) {
                                        // print_r($fullrow[1] . ' ' . $c->getClient()->getRaisonSociale() . ' ' . array_values($clinet)[0]->getRaisonSociale());
                                        $newEntete->setClient(array_values($clinet)[0]);
                                    } else {
                                        $client = new \TimSoft\GeneralBundle\Entity\Client();
                                        $client->setRaisonSociale($fullrow[1]);
                                        $em->persist($client);
                                        $newEntete->setClient($client);
                                    }
                                    $duplicateEC[] = array($c, $newEntete);
                                }
//
                                if ($lc->getType() != $fullrow[5] || $lc->getBu() != $fullrow[0] || $lc->getQuantite() != $fullrow[7]
                                    || trim($lc->getLibelle()) != trim($fullrow[6]) || $lc->getmontantHT() != $fullrow[8] || $lc->getQteRestante() != $fullrow[9]) {
                                    $ligne = new PreLigneCommande();
                                    $ligne->setNLigne($fullrow[3]);
                                    $ligne->setType($fullrow[5]);
                                    $ligne->setBu($fullrow[0]);
                                    $ligne->setCommande($c);
                                    $ligne->setQuantite($fullrow[7]);
                                    $ligne->setLibelle($fullrow[6]);
                                    $ligne->setMontantHT($fullrow[8]);
                                    $ligne->setQteRestante($fullrow[9]);
                                    $dup[] = $ligne;
                                    $duplicate[] = array($lc, $ligne);
                                }
                            }
                        }
                    }
                }
                if ($duplicate || $duplicateEC) {
                    $details = Test($duplicateEC, '0');
                    return $this->render('@TimSoftCommande/Default/duplicate.html.twig', array('duplicate' => $duplicate, 'duplicateEC' => $details));
                }
                if ($lignes) {
//                    $new = array_filter($lignes, function ($var) {
//                        return ($var['name'] == 'CarEnquiry');
//                    });
                    $cc = array_column($lignes, 'id', 0);
//                    print_r($lignes);
//                    die();
//                    $message = (new \Swift_Message(''))
//                        ->setFrom(['timplan@timsoft.net' => "Administrateur TimSoft"])
//                        ->setBody('Bonjour ' . $user->getPrenomUtilisateur() . ',
//
//
//<p>Merci de prendre quelques minutes pour le faire,</p>
//
//
//<p>En cas de soucis, veuillez-contacter L\'administrateur Timplan sur l\'adresse Timplan@timsoft.net</p>
//
//
//<p>Cordialement,</p>
//
//            ', 'text/html');
//                    $failedRecipients = [];
//                    $numSent = 0;
//                    $message->addTo($user->getEmail(), $user->getPrenomUtilisateur() . ' ' . $user->getNomUtilisateur());
//                    $numSent += $this->get('mailer')->send($message, $failedRecipients);
                }
                $em->flush();
                $this->addFlash("success", "This is a success message");
                return $this->redirectToRoute('preCommandeList');
            }
        }
        return $this->render('@TimSoftCommande/Default/ImportCmd.html.twig', array('form' => $form->createView()));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/liste", name="preCommandeList")
     */
    public function listerCmdAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        $bus = [];
        $cmd = [];
        $user = $this->getUser();

        //$bus = $user->getBus();
        foreach ($user->getBus() as $b) {
            $bus[] = $b->getLibelle();
        }
//        print_r($bus);
//        die();
//
//
        $oui = [];
        $non = [];
        $commandes = $this->getDoctrine()->getRepository('TimSoftCommandeBundle:PreTeteCommande')->findAll();
        foreach ($commandes as $commande) {
            foreach ($commande->getLignCommandes() as $lc) {
                if (in_array($lc->getBu(), $bus)) {
                    if (!in_array($commande, $oui)) {
                        $oui[] = $commande;
                    }
                }
            }
        }
        if ($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_GESTIONNAIRE') || $user->hasRole('ROLE_CHEF')) {
            return $this->render('@TimSoftCommande/PreCommande/ConsulterCmd.html.twig', array('commandes' => $commandes));
        } else {
            return $this->render('@TimSoftCommande/PreCommande/ConsulterCmd.html.twig', array('commandes' => $oui));
        }
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/afficher/{id}", name="preCommandeAfficher")
     */
    public function afficherCmdAction($id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        $commande = $this->getDoctrine()->getRepository('TimSoftCommandeBundle:PreTeteCommande')->find($id);
        return $this->render('@TimSoftCommande/PreCommande/AfficherCmd.html.twig', array('commande' => $commande));
    }

    public function addUserAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $commande = new TeteCommande();
        $LC = new ArrayCollection();
        $array = explode(",", $request->get('post'));
        if ($request->get('user')) {
            $user = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Utilisateur')->find($request->get('user'));
            foreach ($array as $value) {
                $lc = $this->getDoctrine()->getRepository('TimSoftCommandeBundle:LigneCommande')->find($value);
                if ($lc) {
                    $LC->add($this->getDoctrine()->getRepository('TimSoftCommandeBundle:LigneCommande')->find($value));
                }
//                $lc = $this->getDoctrine()->getRepository('TimSoftCommandeBundle:LigneCommande')->find($value);
//                $commande = $this->getDoctrine()->getRepository('TimSoftCommandeBundle:TeteCommande')->find($lc->getId());
//                var_dump($this->getDoctrine()->getRepository('TimSoftCommandeBundle:LigneCommande')->find($value)->getId());
//                die();
            }
//            print_r($array);
//            die();
            $plannings = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Planning')->findAll();
            return $this->render('@TimSoftCommande/Default/calander.html.twig', array('cc' => $user, 'lc' => $LC, 'plannings' => $plannings));
        }

        return $this->forward('TimSoftCommandeBundle:Default:listerCmd');
    }

    public function remplacerAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if ($request->get('lc') == 'true') {
            $ligneCommande = $this->getDoctrine()->getRepository('TimSoftCommandeBundle:PreLigneCommande')->find($request->get('id'));
            $ligneCommande->setBu($request->get('bu'));
            $ligneCommande->setQuantite($request->get('quantite'));
            $ligneCommande->setLibelle($request->get('libelle'));
            $ligneCommande->setMontantHT($request->get('montantHT'));
            $ligneCommande->setQteRestante($request->get('quantiteRestante'));
            $em->merge($ligneCommande);
        } else {
            $enteteCommande = $this->getDoctrine()->getRepository('TimSoftCommandeBundle:PreTeteCommande')->find($request->get('id'));
            $client = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Client')->findOneByRaisonSociale($request->get('client'));
            $enteteCommande->setClient($client);
            $em->merge($enteteCommande);
        }
        $em->flush();
        return new JsonResponse('Success');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/modifier/ligne", name="preCommandeModifierLigne")
     */
    public function modifierAction(Request $request)
    {
        $ligne = $this->getDoctrine()->getRepository('TimSoftCommandeBundle:PreLigneCommande')->find($request->get('id'));
        $ligne->setQuantite($request->get('Quantite'));
        $ligne->setLibelle($request->get('libelle'));
        $ligne->setMontantHT($request->get('MontantHT'));
        $ligne->setQteRestante($request->get('Qrestante'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($ligne);
        $em->flush();
        return new JsonResponse($ligne);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/supprimer/ligne", name="preCommandeSupprimerLigne")
     */
    public function supprimerLCAction(Request $request)
    {
        $ligne = $this->getDoctrine()->getRepository('TimSoftCommandeBundle:LigneCommande')->find($request->get('id'));
        $em = $this->getDoctrine()->getManager();
        $em->remove($ligne);
        $em->flush();
        return new Response('success');
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/modifier/{id}", name="preCommandeModifier")
     */
    public function modifierCAction(Request $request)
    {
        $teteCommande = $this->getDoctrine()->getRepository('TimSoftCommandeBundle:PreTeteCommande')->find($request->get('id'));
        $form = $this->createForm(PreTeteCommandeType::class, $teteCommande);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            $teteCommande = $form->getData();
            $em->persist($teteCommande);
            $em->flush();
//            var_dump($teteCommande->getDatePiece());
//            die();
            return $this->redirectToRoute('preCommandeAfficher', ['id' => $teteCommande->getId()]);
        }
        return $this->render('@TimSoftCommande/PreCommande/modelModifier.html.twig', [
            'form' => $form->createView(),
            'commande' => $teteCommande
        ]);
    }

    /**
     * @Route("/generer/{id}", name="preCommandeGenerer")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function genererAction(Request $request)
    {
        $teteCommande = $this->getDoctrine()->getRepository('TimSoftCommandeBundle:PreTeteCommande')->find($request->get('id'));
        $old = clone $teteCommande;
        $form = $this->createForm(TeteCommandeType::class, $teteCommande);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            $newT = new TeteCommande();
            $new = $form->getData();
            $newT->convert($new);
            foreach ($new->getLignCommandes() as $lignCommande) {
                $newL = new LigneCommande();
                $newL->convert($lignCommande);
                $newL->setCommande($newT);
                $em->persist($newL);
                if ($lignCommande->getPlannings()) {
                    foreach ($lignCommande->getPlannings() as $planning) {
                        $planning->setLc($newL);
                        $feuille = $planning->getFeuille();
                        if ($feuille) {
                            $feuille->setNumeroCommande($new->getNCommande());
                            $em->persist($feuille);
                        }
                        $em->persist($planning);
                    }
                }
                $newT->addLigne($newL);
                $em->detach($lignCommande);

            }
            $em->detach($teteCommande);
            $em->persist($newT);
            $em->flush();
            $this->forward('TimSoftCommandeBundle:PreCommande:archive', ['id' => $teteCommande->getId(), 'old' => $newT->getId()]);
            return $this->redirectToRoute('AfficherCmd', ['id' => $newT->getId()]);
        }
        return $this->render('@TimSoftCommande/PreCommande/modelGenerer.html.twig', [
            'form' => $form->createView(),
            'commande' => $teteCommande
        ]);
    }

    /**
     * @param PreTeteCommande $preTeteCommande
     * @param TeteCommande $teteCommande
     * @Route("/archive/{id}", name="preCommandeArhiver")
     */
    public function archiveAction(PreTeteCommande $preTeteCommande, TeteCommande $teteCommande)
    {
        $em = $this->getDoctrine()->getManager();
        $preTeteCommande->setCommande($teteCommande);
        $preTeteCommande->setArchive(true);
        $em->persist($preTeteCommande);
        $em->flush();

    }

    function cast($destination, $sourceObject)
    {
        if (is_string($destination)) {
            $destination = new $destination();
        }
        $sourceReflection = new \ReflectionObject($sourceObject);
        $destinationReflection = new \ReflectionObject($destination);
        $sourceProperties = $sourceReflection->getProperties();
        foreach ($sourceProperties as $sourceProperty) {
            $sourceProperty->setAccessible(true);
            $name = $sourceProperty->getName();
            $value = $sourceProperty->getValue($sourceObject);
            if ($destinationReflection->hasProperty($name)) {
                $propDest = $destinationReflection->getProperty($name);
                $propDest->setAccessible(true);
                $propDest->setValue($destination, $value);
            } else {
                $destination->$name = $value;
            }
        }
        return $destination;
    }

    /**
     * @return JsonResponse
     * @Route("/all", name="getPreCommandes")
     */
    public function getCommandesAction()
    {
        $bus = [];
        $cmds = [];
        $user = $this->getUser();
        $cc = [];
        //$bus = $user->getBus();
        if ($user) {
            foreach ($user->getBus() as $b) {
                $bus[] = $b->getLibelle();
            }
        }
        $commandes = $this->getDoctrine()->getRepository('TimSoftCommandeBundle:PreTeteCommande')->findAll();
        foreach ($commandes as $commande) {
            $cmd = $commande->getbyBus($bus);
            if ($cmd) {
                $cmds[] = $cmd;
            }
        }

        if ($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_GESTIONNAIRE') || $user->hasRole('ROLE_CHEF')) {
//            return $this->render('@TimSoftCommande/Default/ConsulterCmd.html.twig', array('commandes' => $commandes));
            return new JsonResponse(null);
        } else {
            return new JsonResponse($cmds);
        }
    }
}