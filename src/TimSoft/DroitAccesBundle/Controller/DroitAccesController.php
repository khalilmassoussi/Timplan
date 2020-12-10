<?php

namespace TimSoft\DroitAccesBundle\Controller;

use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use TimSoft\GeneralBundle\Entity\DroitAccesGroupe;
use TimSoft\GeneralBundle\Entity\DroitAccesPersonne;

class DroitAccesController extends Controller
{

    /**
     * @Route("/ConsulterDroitAcces", name="ConsulterDroitAcces")
     */
    public function consulterDroitAccesAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Droits = $em->getRepository('TimSoftGeneralBundle:DroitAcces')->getAllDroit();
        return $this->render('@TimSoftDroitAcces/DroitAcces/ConsulterDroitAcces.html.twig', array('Droits' => $Droits));
    }

    /**
     * @Route("/GategoriesSansDroit/{id}", name="CategorieSansDroit" ,options={"expose"=true})
     */
    public function getCategorieSansDroit($id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager();
        $AllCategories = ['ROLE_ADMIN', 'ROLE_GESTIONNAIRE', 'ROLE_CLIENT', 'ROLE_CONSULTANT', 'ROLE_CHEF', 'ROLE_TRACKING', 'ROLE_SUPPORT', 'ROLE_EXTERNE'];
        $categories = $em->getRepository('TimSoftGeneralBundle:DroitAccesGroupe')->getGroupeAutorises($id);
        //pour applatir $Categorie ( car c'est un array a 2 dim)
        $it = new RecursiveIteratorIterator(new RecursiveArrayIterator($categories));
        $cat = array();
        foreach ($it as $v) {
            $cat[] = $v;
        }
        $categorieToModify = array_diff($AllCategories, $cat);
        $categorie = Array();
        foreach ($categorieToModify as $Role) {
            if ($Role == "ROLE_ADMIN") {
                $categorie[] = ["ROLE_ADMIN", "Administrateur"];
            } elseif ($Role == "ROLE_GESTIONNAIRE") {
                $categorie[] = ["ROLE_GESTIONNAIRE", "Gestionnaire"];
            } elseif ($Role == "ROLE_CONSULTANT") {
                $categorie[] = ["ROLE_CONSULTANT", "Consultant"];
            } elseif ($Role == "ROLE_CLIENT") {
                $categorie[] = ["ROLE_CLIENT", "Client"];
            } elseif ($Role == "ROLE_CHEF") {
                $categorie[] = ["ROLE_CHEF", "BU Manager"];
            } elseif ($Role == "ROLE_TRACKING") {
                $categorie[] = ["ROLE_TRACKING", "Tracking User"];
            } elseif ($Role == "ROLE_SUPPORT") {
                $categorie[] = ["ROLE_SUPPORT", "Support"];
            } elseif ($Role == "ROLE_EXTERNE") {
                $categorie[] = ["ROLE_EXTERNE", "Consultant Externe"];
            }
        }
        return new JsonResponse($categorie);
    }

    /**
     * @Route("/GategoriesAvecDroit/{id}", name="CategorieAvecDroit" ,options={"expose"=true})
     */
    public function getCategorieAvecDroit($id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('TimSoftGeneralBundle:DroitAccesGroupe')->getGroupeAutorises($id);
        //pour applatir $Categorie ( car c'est un array a 2 dim
        $it = new RecursiveIteratorIterator(new RecursiveArrayIterator($categories));
        $cat = array();
        foreach ($it as $v) {
            $cat[] = $v;
        }
        $categorie = Array();
        foreach ($cat as $Role) {
            if ($Role == "ROLE_ADMIN") {
                $categorie[] = ["ROLE_ADMIN", "Administrateur"];
            } elseif ($Role == "ROLE_GESTIONNAIRE") {
                $categorie[] = ["ROLE_GESTIONNAIRE", "Gestionnaire"];
            } elseif ($Role == "ROLE_CONSULTANT") {
                $categorie[] = ["ROLE_CONSULTANT", "Consultant"];
            } elseif ($Role == "ROLE_CLIENT") {
                $categorie[] = ["ROLE_CLIENT", "Client"];
            } elseif ($Role == "ROLE_CHEF") {
                $categorie[] = ["ROLE_CHEF", "BU Manager"];
            } elseif ($Role == "ROLE_TRACKING") {
                $categorie[] = ["ROLE_TRACKING", "Tracking User"];
            } elseif ($Role == "ROLE_SUPPORT") {
                $categorie[] = ["ROLE_SUPPORT", "Support"];
            } elseif ($Role == "ROLE_EXTERNE") {
                $categorie[] = ["ROLE_EXTERNE", "Consultant Externe"];
            }
        }
        return new JsonResponse($categorie);
    }

    /**
     * @Route("/UsersSansDroit/{id}", name="UsersSansDroit" ,options={"expose"=true})
     */
    public function selectUsersSansDroitAction(Request $request, $id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
            $AllUsers = $em->getRepository('TimSoftGeneralBundle:Utilisateur')->findAll();
            $UtilisateursAvec = $em->getRepository('TimSoftGeneralBundle:Utilisateur')->OntDroit($id);
            $UserInterdits = $em->getRepository('TimSoftGeneralBundle:DroitAccesPersonne')->getPersonneExceptionnelle($id, 0);
            $UtilisateursAvecDroit = array_diff($UtilisateursAvec, $UserInterdits);
            $UtilisateursSansDroit = array_diff($AllUsers, $UtilisateursAvecDroit);

            return new JsonResponse($UtilisateursSansDroit);
        }
    }

    /**
     * @Route("/UsersAvecDroit/{id}", name="UsersAvecDroit" ,options={"expose"=true})
     */
    public function selectUsersAvecDroitAction(Request $request, $id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
            $UtilisateursAvec = $em->getRepository('TimSoftGeneralBundle:Utilisateur')->OntDroit($id);
            $UserInterdits = $em->getRepository('TimSoftGeneralBundle:DroitAccesPersonne')->getPersonneExceptionnelle($id, 0);
            $UtilisateursAvecDroit = array_diff($UtilisateursAvec, $UserInterdits);
        }
        return new JsonResponse($UtilisateursAvecDroit);
    }

    /**
     * @Route("/ModifierDroitAcces/{id}", name="ModifierDroitAcces")
     */
    public function modifierDroitAccesAction(Request $request, $id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $Droit = $em->getRepository('TimSoftGeneralBundle:DroitAcces')->findOneBy(array('id' => $id));
        $GroupeAutorise = $em->getRepository('TimSoftGeneralBundle:DroitAccesGroupe')->getGroupeAutorises($id);
        $UtilisateurExceptionnelsOK = $em->getRepository('TimSoftGeneralBundle:DroitAccesPersonne')->getPersonneExceptionnelle($id, 1);
        $UtilisateurExceptionnelsKO = $em->getRepository('TimSoftGeneralBundle:DroitAccesPersonne')->getPersonneExceptionnelle($id, 0);

        $IDSuppression = Array(18, 4, 74, 12, 8);

        $DroitAccesPersonne = new DroitAccesPersonne();
        $form = $this->createForm('TimSoft\GeneralBundle\Form\DroitAccesPersonneType', $DroitAccesPersonne);
        $DroitAccesGroupe = new DroitAccesGroupe();
        $form1 = $this->createForm('TimSoft\GeneralBundle\Form\DroitAccesGroupeType', $DroitAccesGroupe);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $form1->handleRequest($request);
        }

        if (in_array($id, $IDSuppression)) {
            $DroitAccesPersonne2 = new DroitAccesPersonne();
            if ($id === '18') {
                $DroitSuppression = $em->getRepository('TimSoftGeneralBundle:DroitAcces')->findOneBy(array('id' => 36));
            }
            if ($id === '4') {
                $DroitSuppression = $em->getRepository('TimSoftGeneralBundle:DroitAcces')->findOneBy(array('id' => 33));
            }
            if ($id === '8') {
                $DroitSuppression = $em->getRepository('TimSoftGeneralBundle:DroitAcces')->findOneBy(array('id' => 86));
            }
            if ($id === '74') {
                $DroitSuppression = $em->getRepository('TimSoftGeneralBundle:DroitAcces')->findOneBy(array('id' => 73));
            }
            if ($id === '12') {
                $DroitSuppression = $em->getRepository('TimSoftGeneralBundle:DroitAcces')->findOneBy(array('id' => 27));

            }
        }


        if ($form->isValid() && ($request->request->get('BoutonModifierDroit') === 'PS')) {
            $Autorisation = $form->get('autorisation')->getData();
            $Utilisateur = $em->getRepository('TimSoftGeneralBundle:Utilisateur')->findOneBy(array('id' => $request->request->get('Users')));

            if ($Autorisation == '1') {

                $DroitAccesPersonne->setDroit($Droit);
                $DroitAccesPersonne->setUtilisateur($Utilisateur);
                $DroitAccesPersonne->setAutorisation(true);
                $DroitAccesPersonne->setRoleUtilisateur($Utilisateur->getRole());
                $DroitUser = $em->getRepository("TimSoftGeneralBundle:DroitAccesPersonne")->findOneBy(array('droit' => $Droit, 'utilisateur' => $Utilisateur));
                if ($DroitUser != null) {
//                    print_r('cc');
//                    die();
//                    if ($Droit->getRouteDroit() == 'addUserToCmd' || $Droit->getRouteDroit() == 'getByBu' || $Droit->getRouteDroit() == 'mesPlans') {
//                        $droits = $em->getRepository("TimSoftGeneralBundle:DroitAcces")->findBy(array('technique' => '1'));
//                        foreach ($droits as $obje) {
//                            $exist = $em->getRepository('TimSoftGeneralBundle:DroitAccesPersonne')->findBy((array('droit' => $obje, 'utilisateur' => $Utilisateur)));
//                            if (!$exist) {
//                                $droitAcces = new DroitAccesPersonne();
//                                $droitAcces->setDroit($obje);
//                                $droitAcces->setUtilisateur($Utilisateur);
//                                $droitAcces->setAutorisation(true);
//                                $droitAcces->setRoleUtilisateur($Utilisateur->getRole());
//                                $em->persist($droitAcces);
//                                print_r($droitAcces);
//                                die();
//                            }
//                        }
////                    print_r($exist);
//                        //print_r($droits);
////                    die();
//                    }
                    $em->remove($DroitUser);
                    $DroitUser2 = $em->getRepository("TimSoftGeneralBundle:DroitAccesPersonne")->findOneBy(array('droit' => $DroitSuppression, 'utilisateur' => $Utilisateur));
                    $em->remove($DroitUser2);
                } else {
                    if (!in_array($Utilisateur->getRoleUtilisateur(), $GroupeAutorise)) {
//                        print_r('ss');
//                        die();
                        if ($Droit->getRouteDroit() == 'addUserToCmd' || $Droit->getRouteDroit() == 'getByBu' || $Droit->getRouteDroit() == 'mesPlans' || $Droit->getRouteDroit() == 'planningByUser') {
                            $droits = $em->getRepository("TimSoftGeneralBundle:DroitAcces")->findBy(array('technique' => '1'));
                            foreach ($droits as $obje) {
                                $exist = $em->getRepository('TimSoftGeneralBundle:DroitAccesPersonne')->findBy((array('droit' => $obje, 'utilisateur' => $Utilisateur)));
                                if (!$exist) {
                                    $droitAcces = new DroitAccesPersonne();
                                    $droitAcces->setDroit($obje);
                                    $droitAcces->setUtilisateur($Utilisateur);
                                    $droitAcces->setAutorisation(true);
                                    $droitAcces->setRoleUtilisateur($Utilisateur->getRole());
                                    $em->persist($droitAcces);
//                                    print_r($Utilisateur->getId());
                                    //   die();
                                }
                            }
//                    print_r($exist);
                            //print_r($droits);
//                            print_r("ccc");
//                            die();
                        }
                        $em->persist($DroitAccesPersonne);
                        if (in_array($id, $IDSuppression)) {
                            $DroitAccesPersonne2->setDroit($DroitSuppression);
                            $DroitAccesPersonne2->setUtilisateur($Utilisateur);
                            $DroitAccesPersonne2->setAutorisation(true);
                            $DroitAccesPersonne2->setRoleUtilisateur($Utilisateur->getRole());
                            $em->persist($DroitAccesPersonne2);
                        }

                    }
                }
            } else {
                $ToDelete = $em->getRepository("TimSoftGeneralBundle:DroitAccesPersonne")->findOneBy(array('droit' => $Droit, 'utilisateur' => $Utilisateur));
                if ($ToDelete != null) {
                    $em->remove($ToDelete); //L'enregistrement se fait uniquement dans Doctrine
                    if (in_array($id, $IDSuppression)) {
                        $ToDelete2 = $em->getRepository("TimSoftGeneralBundle:DroitAccesPersonne")->findOneBy(array('droit' => $DroitSuppression, 'utilisateur' => $Utilisateur));
                        $em->remove($ToDelete2);
                    }

                } else {
//                    print_r($Utilisateur);
//                    die();
                    $DroitAccesPersonne->setDroit($Droit);
                    $DroitAccesPersonne->setUtilisateur($Utilisateur);
                    $DroitAccesPersonne->setAutorisation(false);
                    $DroitAccesPersonne->setRoleUtilisateur($Utilisateur->getRole());
                    $em->persist($DroitAccesPersonne); //L'enregistrement se fait uniquement dans Doctrine
                    if (in_array($id, $IDSuppression)) {
//                        print_r($Utilisateur);
//                        die();
                        $DroitAccesPersonne2->setDroit($DroitSuppression);
                        $DroitAccesPersonne2->setUtilisateur($Utilisateur);
                        $DroitAccesPersonne2->setAutorisation(false);
                        $DroitAccesPersonne2->setRoleUtilisateur($Utilisateur->getRole());
                        $em->persist($DroitAccesPersonne2);
                    }

                }
            }
            $em->flush(); // pour sauvegarder les données dans la BD
            $this->addFlash('OK-Modification', 'Le droit d\'accès "' . $Droit->getNomDroit() . '" est modifié avec succès !');
            return $this->redirectToRoute('ModifierDroitAcces', array('id' => $Droit->getId()));
        }

        if ($form->isValid() && ($request->request->get('BoutonModifierDroit') === 'PUG')) {


//            if ($Droit->getRouteDroit() == 'getByBu') {
//                $droits = $em->getRepository("TimSoftGeneralBundle:DroitAcces")->findOneBy(array('technique' => '1'));
//                foreach ($droits as $droit) {
//                    $droitAcces = new DroitAccesGroupe();
//                    $DroitAccesGroupe->setDroit($droit);
//                    $DroitAccesGroupe->setRole($request->request->get('Categories'));
//                }
//
//                var_dump($form1->get('autorisationCategorie')->getData(), $request->request->get('Categories'), $DroitAccesGroupe);
//                die();
//            }

            $Autorisation = $form1->get('autorisationCategorie')->getData();
            if ($Autorisation == '1') {
//                if ($Droit->getRouteDroit() == 'listerCmd') {
////                    $droit = $em->getRepository("TimSoftGeneralBundle:DroitAcces")->findBy(array('RouteDroit' => 'AfficherCmd'));
////                    $droitAcces = new DroitAccesGroupe();
////                    $droitAcces->setDroit($droit);
////                    $droitAcces->setRole($request->request->get('Categories'));
////                    $em->persist($droitAcces);
////                }
                if ($Droit->getRouteDroit() == 'addUserToCmd' || $Droit->getRouteDroit() == 'getByBu' || $Droit->getRouteDroit() == 'mesPlans' || $Droit->getRouteDroit() == 'planningByUser') {
                    $droits = $em->getRepository("TimSoftGeneralBundle:DroitAcces")->findBy(array('technique' => '1'));
                    foreach ($droits as $obje) {
                        $exist = $em->getRepository('TimSoftGeneralBundle:DroitAccesGroupe')->findBy((array('droit' => $obje, 'role' => $request->request->get('Categories'))));
                        if (!$exist) {
                            $droitAcces = new DroitAccesGroupe();
                            $droitAcces->setDroit($obje);
                            $droitAcces->setRole($request->request->get('Categories'));
                            $em->persist($droitAcces);
//                            print_r($droitAcces);
                        }
                    }
//                    print_r($exist);
                    //print_r($droits);
//                    die();
                }
                $DroitAccesGroupe->setDroit($Droit);
                $DroitAccesGroupe->setRole($request->request->get('Categories'));
                $em->persist($DroitAccesGroupe);

                if (in_array($id, $IDSuppression)) {
                    $DroitAccesGroupe2 = new DroitAccesGroupe();
                    $DroitAccesGroupe2->setDroit($DroitSuppression);
                    $DroitAccesGroupe2->setRole($request->request->get('Categories'));
                    $em->persist($DroitAccesGroupe2);
                }

            } else {
                $ToDelete = $em->getRepository("TimSoftGeneralBundle:DroitAccesGroupe")->findOneBy(array('droit' => $Droit, 'role' => $request->request->get('Categories')));
                if ($Droit->getRouteDroit() == 'listerCmd') {
                    $droit = $em->getRepository("TimSoftGeneralBundle:DroitAcces")->findBy(array('routeDroit' => 'AfficherCmd'));
                    $droitAcces = $em->getRepository("TimSoftGeneralBundle:DroitAccesGroupe")->findOneBy(array('droit' => $droit, 'role' => $request->request->get('Categories')));
                    if ($droitAcces) {
                        $em->remove($droitAcces);
                    }
                }
                $em->remove($ToDelete); //L'enregistrement se fait uniquement dans Doctrine
                if (in_array($id, $IDSuppression)) {
                    $ToDelete2 = $em->getRepository("TimSoftGeneralBundle:DroitAccesGroupe")->findOneBy(array('droit' => $DroitSuppression, 'role' => $request->request->get('Categories')));
                    $em->remove($ToDelete2);
                }
            }


            $em->flush(); // pour sauvegarder les données dans la BD
            $this->addFlash('OK-Modification', 'Le droit d\'accès "' . $Droit->getNomDroit() . '" est modifié avec succès !');
            return $this->redirectToRoute('ModifierDroitAcces', array('id' => $Droit->getId()));
        }
        return $this->render('@TimSoftDroitAcces/DroitAcces/ModifierDroitAcces.html.twig', array('form' => $form->createView(), 'form1' => $form1->createView(), 'Droit' => $Droit, 'GroupeAutorise' => $GroupeAutorise, 'UtilisateurExceptionnelsOK' => $UtilisateurExceptionnelsOK, 'UtilisateurExceptionnelsKO' => $UtilisateurExceptionnelsKO));
    }
}
