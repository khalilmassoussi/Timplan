<?php

namespace TimSoft\DroitAccesBundle\Extensions;

use Symfony\Bridge\Doctrine\RegistryInterface;
use TimSoft\CommandeBundle\Entity\LigneCommande;
use TimSoft\CommandeBundle\Entity\PreLigneCommande;
use TimSoft\CommandeBundle\Entity\PreTeteCommande;
use TimSoft\CommandeBundle\Entity\TeteCommande;

class TwigExtensions extends \Twig_Extension
{
    public function getFunctions()
    {
        // Register the function in twig :
        // In your template you can use it as : {{find(123)}}
        return array(
            new \Twig_SimpleFunction('autoriserGroupe', array($this, 'autoriserGroupe')),
            new \Twig_SimpleFunction('contient', array($this, 'contient')),
            new \Twig_SimpleFunction('NBUtilisateur', array($this, 'NBUtilisateur')),
            new \Twig_SimpleFunction('NBFacture', array($this, 'NBFacture')),
            new \Twig_SimpleFunction('NBFactureEnCours', array($this, 'NBFactureEnCours')),
            new \Twig_SimpleFunction('NBClient', array($this, 'NBClient')),
            new \Twig_SimpleFunction('NBFactureEnCours', array($this, 'NBFactureEnCours')),
            new \Twig_SimpleFunction('NBFactureNonTraite', array($this, 'NBFactureNonTraite')),
            new \Twig_SimpleFunction('NBFactureValide', array($this, 'NBFactureValide')),
            new \Twig_SimpleFunction('NBFactureReglementEnCours', array($this, 'NBFactureReglementEnCours')),
            new \Twig_SimpleFunction('NBFactureReglementPret', array($this, 'NBFactureReglementPret')),
            new \Twig_SimpleFunction('NBFactureRefusee', array($this, 'NBFactureRefusee')),
            new \Twig_SimpleFunction('NBFeuilleValidee', array($this, 'NBFeuilleValidee')),
            new \Twig_SimpleFunction('NBFeuilleDePresence', array($this, 'NBFeuilleDePresence')),
            new \Twig_SimpleFunction('NBFeuilleNonValidee', array($this, 'NBFeuilleNonValidee')),
            new \Twig_SimpleFunction('NBRapport', array($this, 'NBRapport')),
            new \Twig_SimpleFunction('NBRapportValide', array($this, 'NBRapportValide')),
            new \Twig_SimpleFunction('NBRapportNonValide', array($this, 'NBRapportNonValide')),
            new \Twig_SimpleFunction('UtilisateurDeactive', array($this, 'UtilisateurDeactive')),
            new \Twig_SimpleFunction('UtilisateurActive', array($this, 'UtilisateurActive')),
            new \Twig_SimpleFunction('UtilisateurNonValide', array($this, 'UtilisateurNonValide')),
            new \Twig_SimpleFunction('RapportClient', array($this, 'RapportClient')),
            new \Twig_SimpleFunction('NBFactureClient', array($this, 'NBFactureClient')),
            new \Twig_SimpleFunction('FeuilleParClient', array($this, 'FeuilleParClient')),
            new \Twig_SimpleFunction('FactureEnCoursParClient', array($this, 'FactureEnCoursParClient')),
            new \Twig_SimpleFunction('FactureParStatutParClient', array($this, 'FactureParStatutParClient')),
            new \Twig_SimpleFunction('FeuilleParValidationParClient', array($this, 'FeuilleParValidationParClient')),
            new \Twig_SimpleFunction('FeuilleParConsultant', array($this, 'FeuilleParConsultant')),
            new \Twig_SimpleFunction('FeuilleParStatutParConsultant', array($this, 'FeuilleParStatutParConsultant')),
            new \Twig_SimpleFunction('RapportParValidationParConsultant', array($this, 'RapportParValidationParConsultant')),
            new \Twig_SimpleFunction('RapportParConsultant', array($this, 'RapportParConsultant')),
            new \Twig_SimpleFunction('RapportParValidationParClient', array($this, 'RapportParValidationParClient')),
            new \Twig_SimpleFunction('afficherRubrique', array($this, 'afficherRubrique')),
            new \Twig_SimpleFunction('AutorisationAcces', array($this, 'AutorisationAcces')),
            new \Twig_SimpleFunction('NotificationVu', array($this, 'NotificationVu')),
            new \Twig_SimpleFunction('NotificationNonVu', array($this, 'NotificationNonVu')),
            new \Twig_SimpleFunction('Notifications', array($this, 'Notifications')),
            new \Twig_SimpleFunction('RapportParTypeParClient', array($this, 'RapportParTypeParClient')),
            new \Twig_SimpleFunction('RapportParTypeParConsultant', array($this, 'RapportParTypeParConsultant')),
            new \Twig_SimpleFunction('getUsers', array($this, 'getUsers')),
            new \Twig_SimpleFunction('getUsersByBu', array($this, 'getUsersByBu')),
        );
    }

    protected $doctrine;

    // Retrieve doctrine from the constructor
    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function contient($ID, $Role)
    {
        $em = $this->doctrine->getManager();
        $myRepo = $em->getRepository('TimSoftGeneralBundle:DroitAccesPersonne');
        return $myRepo->contientException($ID, $Role);
    }

    public function autoriserGroupe($ID, $Groupe)
    {
        $em = $this->doctrine->getManager();
        $myRepo = $em->getRepository('TimSoftGeneralBundle:DroitAccesGroupe');
        return $myRepo->autoriserGroupe($ID, $Groupe);
    }

    public function NBUtilisateur()
    {
        $em = $this->doctrine->getManager();
        $myRepo = $em->getRepository('TimSoftGeneralBundle:Utilisateur');
        return $myRepo->getNBUtilisateur();
    }

    public function NotificationNonVu($Personne)
    {
        $em = $this->doctrine->getManager();
        $NotificationNonVu = $em->getRepository("TimSoftGeneralBundle:NotificationUtilisateur")->findBy(array('Utilisateur' => $Personne, 'vu' => 0));
        return $NotificationNonVu;
    }

    public function NotificationVu($Personne)
    {
        $em = $this->doctrine->getManager();
        $NotificationVu = $em->getRepository("TimSoftGeneralBundle:NotificationUtilisateur")->findBy(array('Utilisateur' => $Personne, 'vu' => 1));
        return $NotificationVu;
    }

    public function Notifications($Personne)
    {
        $em = $this->doctrine->getManager();
        $Notifications = $em->getRepository("TimSoftGeneralBundle:NotificationUtilisateur")->findBy(array('Utilisateur' => $Personne, 'vu' => 0), array('id' => 'DESC'));
        return $Notifications;
    }

    public function UtilisateurActive()
    {
        $em = $this->doctrine->getManager();
        $myRepo = $em->getRepository('TimSoftGeneralBundle:Utilisateur');
        return $myRepo->findBy(array('statutProfil' => 1, 'enabled' => 1, 'confirmationToken' => null));
    }

    public function UtilisateurDeactive()
    {
        $em = $this->doctrine->getManager();
        $myRepo = $em->getRepository('TimSoftGeneralBundle:Utilisateur');
        return $myRepo->findBy(array('statutProfil' => 0));
    }

    public function UtilisateurNonValide()
    {
        $em = $this->doctrine->getManager();
        $myRepo = $em->getRepository('TimSoftGeneralBundle:Utilisateur');
        return $myRepo->findBy(array('statutProfil' => 1, 'enabled' => 0));
    }

    public function NBClient()
    {
        $em = $this->doctrine->getManager();
        $NBClient = $em->getRepository('TimSoftGeneralBundle:Client')->getNBCLient();
        return $NBClient;
    }

    public function NBFacture()
    {
        $em = $this->doctrine->getManager();
        $NBFacture = $em->getRepository('TimSoftGeneralBundle:Facture')->getNBFacture();
        return $NBFacture;
    }

    public function NBFactureClient($Client)
    {
        $em = $this->doctrine->getManager();
        $NBFacture = $em->getRepository('TimSoftGeneralBundle:Facture')->getNBFactureClient($Client);
        return $NBFacture;
    }

    public function NBFactureEnCours()
    {
        $em = $this->doctrine->getManager();
        $NBFactureEnCours = $em->getRepository('TimSoftGeneralBundle:Facture')->getNBFactureEnCours();
        return $NBFactureEnCours;
    }

    public function NBFactureNonTraite()
    {
        $em = $this->doctrine->getManager();
        $NBFactureNonTraite = $em->getRepository('TimSoftGeneralBundle:Facture')->getNBFactureParStatut('Non traitée');
        return $NBFactureNonTraite;
    }

    public function NBFactureValide()
    {
        $em = $this->doctrine->getManager();
        $NBFactureValide = $em->getRepository('TimSoftGeneralBundle:Facture')->getNBFactureParStatut('Validée');
        return $NBFactureValide;
    }

    public function NBFactureReglementEnCours()
    {
        $em = $this->doctrine->getManager();
        $NBFactureReglementEnCours = $em->getRepository('TimSoftGeneralBundle:Facture')->getNBFactureParStatut('Règlement en cours');
        return $NBFactureReglementEnCours;
    }

    public function NBFactureReglementPret()
    {
        $em = $this->doctrine->getManager();
        $NBFactureReglementPret = $em->getRepository('TimSoftGeneralBundle:Facture')->getNBFactureParStatut('Règlement prêt');
        return $NBFactureReglementPret;
    }

    public function NBFactureRefusee()
    {
        $em = $this->doctrine->getManager();
        $NBFactureRefusee = $em->getRepository('TimSoftGeneralBundle:Facture')->getNBFactureParStatut('Refusée');
        return $NBFactureRefusee;
    }

    public function NBFeuilleValidee()
    {
        $em = $this->doctrine->getManager();
        $NBFeuilleValidee = $em->getRepository('TimSoftGeneralBundle:FeuilleDePresence')->getNBFeuilleParValidation(1);
        return $NBFeuilleValidee;
    }

    public function NBFeuilleDePresence()
    {
        $em = $this->doctrine->getManager();
        $NBFeuilleDePresence = $em->getRepository('TimSoftGeneralBundle:FeuilleDePresence')->getNBFeuille();
        return $NBFeuilleDePresence;
    }

    public function NBFeuilleNonValidee()
    {
        $em = $this->doctrine->getManager();
        $NBFeuilleNonValidee = $em->getRepository('TimSoftGeneralBundle:FeuilleDePresence')->getNBFeuilleParValidation(0);
        return $NBFeuilleNonValidee;
    }

    public function NBRapport()
    {
        $em = $this->doctrine->getManager();
        $NBRapport = $em->getRepository('TimSoftGeneralBundle:RapportIntervention')->getNBRapport();
        return $NBRapport;
    }

    public function RapportClient($Client)
    {
        $em = $this->doctrine->getManager();
        $NBRapport = $em->getRepository('TimSoftGeneralBundle:RapportIntervention')->getRapportClient($Client);
        return $NBRapport;
    }

    public function NBRapportValide()
    {
        $em = $this->doctrine->getManager();
        $NBRapportValide = $em->getRepository('TimSoftGeneralBundle:RapportIntervention')->getNBRapportParValidation(1);
        return $NBRapportValide;
    }

    public function NBRapportNonValide()
    {
        $em = $this->doctrine->getManager();
        $NBRapportNonValide = $em->getRepository('TimSoftGeneralBundle:RapportIntervention')->getNBRapportParValidation(0);
        return $NBRapportNonValide;
    }

    public function FeuilleParClient($Client)
    {
        $em = $this->doctrine->getManager();
        $FeuilleParClient = $em->getRepository('TimSoftGeneralBundle:FeuilleDePresence')->getFeuilleParClient($Client);
        return $FeuilleParClient;
    }

    public function FactureEnCoursParClient($Client)
    {
        $em = $this->doctrine->getManager();
        $FactureEnCoursParClient = $em->getRepository('TimSoftGeneralBundle:Facture')->getFactureEnCoursCleint($Client);
        return $FactureEnCoursParClient;
    }

    public function FactureParStatutParClient($Statut, $Client)
    {
        $em = $this->doctrine->getManager();
        $Factures = $em->getRepository('TimSoftGeneralBundle:Facture')->getFactureParStatutParClient($Statut, $Client);
        return $Factures;
    }

    public function FeuilleParValidationParClient($Statut, $Client)
    {
        $em = $this->doctrine->getManager();
        $Feuilles = $em->getRepository('TimSoftGeneralBundle:FeuilleDePresence')->getFeuilleParValidationParClient($Statut, $Client);
        return $Feuilles;
    }

    public function RapportParValidationParClient($Statut, $Client)
    {
        $em = $this->doctrine->getManager();
        $Rapports = $em->getRepository('TimSoftGeneralBundle:RapportIntervention')->getRapportParValidationParClient($Statut, $Client);
        return $Rapports;
    }

    public function RapportParTypeParClient($type, $Client)
    {
        $em = $this->doctrine->getManager();
        $Rapports = $em->getRepository('TimSoftGeneralBundle:RapportIntervention')->getRapportParTypeParClient($type, $Client);
        return $Rapports;
    }

    public function RapportParTypeParConsultant($type, $Consultant)
    {
        $em = $this->doctrine->getManager();
        $Rapports = $em->getRepository('TimSoftGeneralBundle:RapportIntervention')->getRapportParTypeParConsultant($type, $Consultant);
        return $Rapports;
    }

    public function FeuilleParConsultant($Consultant)
    {
        $em = $this->doctrine->getManager();
        $Feuilles = $em->getRepository('TimSoftGeneralBundle:FeuilleDePresence')->getFeuilleParConsultant($Consultant);
        return $Feuilles;
    }

    public function FeuilleParStatutParConsultant($Consultant, $Statut)
    {
        $em = $this->doctrine->getManager();
        $Feuilles = $em->getRepository('TimSoftGeneralBundle:FeuilleDePresence')->getFeuilleParStatutParConsultant($Consultant, $Statut);
        return $Feuilles;
    }

    public function RapportParConsultant($Consultant)
    {
        $em = $this->doctrine->getManager();
        $Feuilles = $em->getRepository('TimSoftGeneralBundle:RapportIntervention')->getRapportConsultant($Consultant);
        return $Feuilles;
    }

    public function RapportParValidationParConsultant($Consultant, $Validation)
    {
        $em = $this->doctrine->getManager();
        $Feuilles = $em->getRepository('TimSoftGeneralBundle:RapportIntervention')->getRapportParValidationParConsultant($Consultant, $Validation);
        return $Feuilles;
    }

    public function afficherRubrique($Utilisateur)
    {
        $em = $this->doctrine->getManager();
        $Droits = $em->getRepository('TimSoftGeneralBundle:DroitAcces')->getAllDroit();
        $Rubriques = array();
        foreach ($Droits as $Droit) {
            $d1 = $em->getRepository('TimSoftGeneralBundle:DroitAccesPersonne')->getAutorisationPersonne($Droit->getRouteDroit(), $Utilisateur);
            $d2 = $em->getRepository('TimSoftGeneralBundle:DroitAccesGroupe')->getAutorisationGroupe($Droit->getRouteDroit(), $Utilisateur->getRoleUtilisateur());
            if ($d1 || $d2) {
                $Rubriques[] = $Droit->getCategorieDroit();
            }
        }
        $result = array_unique($Rubriques);
        return $result;
    }


    public function AutorisationAcces($Route, $Utilisateur)
    {
        $em = $this->doctrine->getManager();
        $AutorisationPersonne = $em->getRepository('TimSoftGeneralBundle:DroitAccesPersonne')->getAutorisationPersonne($Route, $Utilisateur);
        $AutorisationGroupe = $em->getRepository('TimSoftGeneralBundle:DroitAccesGroupe')->getAutorisationGroupe($Route, $Utilisateur->getRoleUtilisateur());
        if ($AutorisationPersonne || $AutorisationGroupe) {
            return true;
        } else {
            return false;
        }
    }

    public function getUsersByBu($id)
    {
        $em = $this->doctrine->getManager();
        $bu = $em->getRepository('TimSoftGeneralBundle:Utilisateur')->find($id);
        $users = $em->getRepository('TimSoftGeneralBundle:Utilisateur')->findByBu($bu->getBus());
        return $users;
    }

    public function getUsers()
    {
        $em = $this->doctrine->getManager();
        $users1 = $em->getRepository('TimSoftGeneralBundle:Utilisateur')->findAll();
        return $users1;
    }


    public function getName()
    {
        return 'Twig myCustomName Extensions';
    }

    public function getTests()
    {
        return [
            new \Twig_SimpleTest('PreTeteCommande', function (TeteCommande $teteCommande) {
                return $teteCommande instanceof PreTeteCommande;
            }),
            new \Twig_SimpleTest('PreLigneCommande', function (LigneCommande $ligneCommande) {
                return $ligneCommande instanceof PreLigneCommande;
            })
        ];
    }

}
