<?php

namespace TimSoft\GeneralBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use SBC\NotificationsBundle\Builder\NotificationBuilder;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * FeuilleDePresence
 *
 * @ORM\Table(name="feuille_de_presence")
 * @ORM\Entity(repositoryClass="TimSoft\GeneralBundle\Repository\FeuilleDePresenceRepository")
 * @ORM\MappedSuperclass
 * @Gedmo\Loggable
 *
 */
class FeuilleDePresence implements \JsonSerializable
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @var int
     *
     * @ORM\Column(name="NumeroCommande", type="string")
     */
    private $numeroCommande;

    /**
     * @var string
     *
     * @ORM\Column(name="LibelleCommande", type="text")
     * @Assert\NotBlank()
     * @Assert\Length(min=3, minMessage="Le libellé est très court ! Il doit au moins contenir 3 caractères ")
     *
     */
    private $libelleCommande;

    /**
     * @var string
     *
     * @ORM\Column(name="ModeIntervention", type="string", length=100)
     */
    private $modeIntervention;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DateIntervention", type="date")
     */
    private $dateIntervention;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="HeureDebutInterventionMatin", type="time" , nullable=true)
     */
    private $heureDebutInterventionMatin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="HeureFinInterventionMatin", type="time", nullable=true)
     */
    private $heureFinInterventionMatin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="HeureDebutInterventionAM", type="time", nullable=true)
     */
    private $heureDebutInterventionAM;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="HeureFinInterventionAM", type="time", nullable=true)
     */
    private $heureFinInterventionAM;

    /**
     * @var string
     *
     * @ORM\Column(name="LieuIntervention", type="string", length=255,nullable=true)
     */
    private $lieuIntervention;

    /**
     * @var boolean
     * @Gedmo\Versioned
     * @ORM\Column(name="StatutValidation", type="boolean" , nullable=true)
     */
    private $statutValidation;

    /**
     * @ORM\ManyToOne(targetEntity="Utilisateur")
     * @ORM\JoinColumn(name="Intervenant", referencedColumnName="id", onDelete="CASCADE")
     */
    private $intervenant;

    /**
     * Une Feuille concerne un client
     * @ORM\ManyToOne(targetEntity="Client")
     * @ORM\JoinColumn(name="Client", referencedColumnName="id", onDelete="CASCADE")
     */
    private $client;


    /**
     * Plusieurs personnes participent dans ll'intervention
     * @ORM\OneToMany(targetEntity="Referent", mappedBy="intervention", cascade={"persist"})
     */
    private $participants;

    /**
     * Un rapport est relatif à une feuille de présence
     * @ORM\OneToOne(targetEntity="RapportIntervention", mappedBy="FeuilleDePresence")
     */
    private $rapportIntervention;
    /**
     * @var
     * @ORM\OneToOne(targetEntity="TimSoft\GeneralBundle\Entity\Planning", inversedBy="feuille")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $intervention;
    /**
     * @var
     * @ORM\Column(type="boolean")
     */
    private $done = false;
    /**
     * @var
     * @ORM\Column(type="boolean", nullable=true)
     * @Assert\NotBlank()
     */
    private $validationClient;
    /**
     * @var
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Length(min=8,max=8, exactMessage="Le libellé doit contenir 8 caractères ")
     */
    private $numLivraison;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set numeroCommande
     *
     * @param integer $numeroCommande
     *
     * @return FeuilleDePresence
     */
    public function setNumeroCommande($numeroCommande)
    {
        $this->numeroCommande = $numeroCommande;

        return $this;
    }

    /**
     * Get numeroCommande
     *
     * @return integer
     */
    public function getNumeroCommande()
    {
        return $this->numeroCommande;
    }

    /**
     * Set libelleCommande
     *
     * @param string $libelleCommande
     *
     * @return FeuilleDePresence
     */
    public function setLibelleCommande($libelleCommande)
    {
        $this->libelleCommande = $libelleCommande;

        return $this;
    }

    /**
     * Get libelleCommande
     *
     * @return string
     */
    public function getLibelleCommande()
    {
        return $this->libelleCommande;
    }

    /**
     * Set modeIntervention
     *
     * @param string $modeIntervention
     *
     * @return FeuilleDePresence
     */
    public function setModeIntervention($modeIntervention)
    {
        $this->modeIntervention = $modeIntervention;

        return $this;
    }

    /**
     * Get modeIntervention
     *
     * @return string
     */
    public function getModeIntervention()
    {
        return $this->modeIntervention;
    }

    /**
     * Set dateIntervention
     *
     * @param \DateTime $dateIntervention
     *
     * @return FeuilleDePresence
     */
    public function setDateIntervention($dateIntervention)
    {
        $this->dateIntervention = $dateIntervention;

        return $this;
    }

    /**
     * Get dateIntervention
     *
     * @return \DateTime
     */
    public function getDateIntervention()
    {
        return $this->dateIntervention;
    }

    /**
     * Set heureDebutInterventionMatin
     *
     * @param \DateTime $heureDebutInterventionMatin
     *
     * @return FeuilleDePresence
     */
    public function setHeureDebutInterventionMatin($heureDebutInterventionMatin)
    {
        $this->heureDebutInterventionMatin = $heureDebutInterventionMatin;

        return $this;
    }

    /**
     * Get heureDebutInterventionMatin
     *
     * @return \DateTime
     */
    public function getHeureDebutInterventionMatin()
    {
        return $this->heureDebutInterventionMatin;
    }

    /**
     * Set heureFinInterventionMatin
     *
     * @param \DateTime $heureFinInterventionMatin
     *
     * @return FeuilleDePresence
     */
    public function setHeureFinInterventionMatin($heureFinInterventionMatin)
    {
        $this->heureFinInterventionMatin = $heureFinInterventionMatin;

        return $this;
    }

    /**
     * Get heureFinInterventionMatin
     *
     * @return \DateTime
     */
    public function getHeureFinInterventionMatin()
    {
        return $this->heureFinInterventionMatin;
    }

    /**
     * Set heureDebutInterventionAM
     *
     * @param \DateTime $heureDebutInterventionAM
     *
     * @return FeuilleDePresence
     */
    public function setHeureDebutInterventionAM($heureDebutInterventionAM)
    {
        $this->heureDebutInterventionAM = $heureDebutInterventionAM;

        return $this;
    }

    /**
     * Get heureDebutInterventionAM
     *
     * @return \DateTime
     */
    public function getHeureDebutInterventionAM()
    {
        return $this->heureDebutInterventionAM;
    }

    /**
     * Set heureFinInterventionAM
     *
     * @param \DateTime $heureFinInterventionAM
     *
     * @return FeuilleDePresence
     */
    public function setHeureFinInterventionAM($heureFinInterventionAM)
    {
        $this->heureFinInterventionAM = $heureFinInterventionAM;

        return $this;
    }

    /**
     * Get heureFinInterventionAM
     *
     * @return \DateTime
     */
    public function getHeureFinInterventionAM()
    {
        return $this->heureFinInterventionAM;
    }


    /**
     * Set intervenant
     *
     * @param \TimSoft\GeneralBundle\Entity\Utilisateur $intervenant
     *
     * @return FeuilleDePresence
     */
    public function setIntervenant(\TimSoft\GeneralBundle\Entity\Utilisateur $intervenant = null)
    {
        $this->intervenant = $intervenant;

        return $this;
    }

    /**
     * Get intervenant
     *
     * @return \TimSoft\GeneralBundle\Entity\Utilisateur
     */
    public function getIntervenant()
    {
        return $this->intervenant;
    }

    /**
     * Set client
     *
     * @param \TimSoft\GeneralBundle\Entity\Client $client
     *
     * @return FeuilleDePresence
     */
    public function setClient(\TimSoft\GeneralBundle\Entity\Client $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \TimSoft\GeneralBundle\Entity\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return mixed
     */
    public function getIntervention()
    {
        return $this->intervention;
    }

    /**
     * @param mixed $intervention
     */
    public function setIntervention($intervention)
    {
        $this->intervention = $intervention;
    }

    /**
     * Add participant
     *
     * @param \TimSoft\GeneralBundle\Entity\Referent $participant
     *
     * @return FeuilleDePresence
     */
    public function addParticipant(\TimSoft\GeneralBundle\Entity\Referent $participant)
    {
        $participant->setIntervention($this);
        $participant->setSociete($this->getClient());
        $this->participants->add($participant);

        return $this;
    }

    /**
     * Remove participant
     *
     * @param \TimSoft\GeneralBundle\Entity\Referent $participant
     */
    public function removeParticipant(\TimSoft\GeneralBundle\Entity\Referent $participant)
    {
        $this->participants->removeElement($participant);
    }

    /**
     * Get participants
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getParticipants()
    {
        return $this->participants;
    }

//    public function __construct() {
//        $this->$participants = new ArrayCollection();
//    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->dateIntervention = new \DateTime('now');
//        $this->heureDebutInterventionMatin = new \DateTime();
//        $this->heureFinInterventionMatin= new \DateTime();
//        $this->heureDebutInterventionAM= new \DateTime();
//        $this->heureFinInterventionAM = new \DateTime();
        $this->participants = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set rapportIntervention
     *
     * @param \TimSoft\GeneralBundle\Entity\RapportIntervention $rapportIntervention
     *
     * @return FeuilleDePresence
     */
    public function setRapportIntervention(\TimSoft\GeneralBundle\Entity\RapportIntervention $rapportIntervention = null)
    {
        $this->rapportIntervention = $rapportIntervention;

        return $this;
    }

    /**
     * Get rapportIntervention
     *
     * @return \TimSoft\GeneralBundle\Entity\RapportIntervention
     */
    public function getRapportIntervention()
    {
        return $this->rapportIntervention;
    }

    /**
     * Set statutValidation
     *
     * @param boolean $statutValidation
     *
     * @return FeuilleDePresence
     */
    public function setStatutValidation($statutValidation)
    {
        $this->statutValidation = $statutValidation;

        return $this;
    }

    /**
     * Get statutValidation
     *
     * @return boolean
     */
    public function getStatutValidation()
    {
        return $this->statutValidation;
    }

    /**
     * Set lieuIntervention
     *
     * @param string $lieuIntervention
     *
     * @return FeuilleDePresence
     */
    public function setLieuIntervention($lieuIntervention)
    {
        $this->lieuIntervention = $lieuIntervention;

        return $this;
    }

    /**
     * Get lieuIntervention
     *
     * @return string
     */
    public function getLieuIntervention()
    {
        return $this->lieuIntervention;
    }

    public function __toString()
    {
        return (string)$this->getClient() . ' - ' . $this->getIntervenant() . ' le : ' . $this->getDateIntervention()->format('d-m-Y');
    }


    /**
     * @Assert\Callback(message="doit être supérieure")
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {

        $HDM = $this->getHeureDebutInterventionMatin();
        $HFM = $this->getHeureFinInterventionMatin();
        $HDAM = $this->getHeureDebutInterventionAM();
        $HFAM = $this->getHeureFinInterventionAM();


        if (($HFM === null) && ($HDM !== null)) {
            $context->buildViolation(' Vous Devez entrer l\'heure de fin (Matin) !')
                ->atPath('heureFinInterventionMatin')
                ->addViolation();
        } elseif (($HFM !== null) && ($HDM === null)) {
            $context->buildViolation(' Vous Devez entrer l\'heure de début (Matin) !')
                ->atPath('heureDebutInterventionMatin')
                ->addViolation();
        } elseif (($HFM !== null) && ($HDM !== null) && ($HFM <= $HDM)) {
            $context->buildViolation(' l\'heure de début de l\'intervention (Matin) doit être supérieure à celle de fin!')
                ->atPath('heureDebutInterventionMatin')
                ->addViolation();
        }


        if (($HFAM !== null) && ($HDAM === null)) {
            $context->buildViolation(' Vous Devez entrer l\'heure de début (Après-Midi) !')
                ->atPath('heureDebutInterventionMatin')
                ->addViolation();
        } elseif (($HFAM === null) && ($HDAM !== null)) {
            $context->buildViolation(' Vous Devez entrer l\'heure de fin (Après-Midi) !')
                ->atPath('heureDebutInterventionMatin')
                ->addViolation();
        } elseif (($HFAM !== null) && ($HDAM !== null) && ($HFAM <= $HDAM)) {
            $context->buildViolation(' l\'heure de début de l\'intervention (Après-Midi) doit être supérieure à celle de fin!')
                ->atPath('heureDebutInterventionMatin')
                ->addViolation();
        } elseif (($HFM !== null) && ($HDAM !== null) && ($HDAM <= $HFM)) {
            $context->buildViolation(' l\'heure de début de l\'intervention (Après-Midi) doit être supérieure à celle de fin (Matin)!')
                ->atPath('heureDebutInterventionAM')
                ->addViolation();
        }


    }

    public function buildNotifications(NotificationBuilder $builder)
    {
        return $builder;
    }

    function jsonSerialize()
    {
        $array = array(
            'id' => $this->id,
            'Date' => $this->dateIntervention->format('d-m-Y'),
            'Client' => $this->client->getRaisonSociale(),
            'Intervenant' => $this->intervenant->__toString(),
            'Type' => 'Feuille de présence',
            'numLivraison' => $this->numLivraison,
            'Qte' => $this->intervention->jRestantes()
        );
        if ($this->statutValidation) {
            $array['Validation'] = 'Validé';
        } else {
            $array['Validation'] = 'Non Validé';
        }
        return $array;
    }

    /**
     * Build notifications on entity creation
     * @param NotificationBuilder $builder
     * @return NotificationBuilder
     */
    public function notificationsOnCreate(NotificationBuilder $builder)
    {
        // TODO: Implement notificationsOnCreate() method.
    }

    /**
     * Build notifications on entity update
     * @param NotificationBuilder $builder
     * @return NotificationBuilder
     */
    public function notificationsOnUpdate(NotificationBuilder $builder)
    {
        // TODO: Implement notificationsOnUpdate() method.
    }

    /**
     * Build notifications on entity delete
     * @param NotificationBuilder $builder
     * @return NotificationBuilder
     */
    public function notificationsOnDelete(NotificationBuilder $builder)
    {
        // TODO: Implement notificationsOnDelete() method.
    }

    /**
     * @return mixed
     */
    public function getDone()
    {
        return $this->done;
    }

    /**
     * @param mixed $done
     */
    public function setDone($done)
    {
        $this->done = $done;
    }

    /**
     * @return mixed
     */
    public function getValidationClient()
    {
        return $this->validationClient;
    }

    /**
     * @param mixed $validationClient
     */
    public function setValidationClient($validationClient)
    {
        $this->validationClient = $validationClient;
    }

    /**
     * @return mixed
     */
    public function getNumLivraison()
    {
        return $this->numLivraison;
    }

    /**
     * @param mixed $numLivraison
     */
    public function setNumLivraison($numLivraison)
    {
        $this->numLivraison = $numLivraison;
    }

}
//    public function validate(ExecutionContextInterface $context)
//{
//
//    $HDM=$this->getHeureDebutInterventionMatin();
//    $HFM=$this->getHeureFinInterventionMatin();
//    $HDAM=$this->getHeureDebutInterventionAM();
//    $HFAM=$this->getHeureFinInterventionAM();
//
//    if($HFM->format("H:i") < $HDM->format("H:i"))
//        {
//
//        $context->addViolationAt(
//            'heureDebutInterventionMatin',
//            'Erreur! l\'heure de début de l\'intervention (Matin) doit être supérieure à celle de fin',
//            array(),
//            null
//        );
//    }

//  /**
//     * @Assert\Callback(message="doit être supérieure")
//     */
//    public function validate(ExecutionContextInterface $context, $payload)
//    {
//
//    $HDM=$this->getHeureDebutInterventionMatin();
//    $HFM=$this->getHeureFinInterventionMatin();
//    $HDAM=$this->getHeureDebutInterventionAM();
//    $HFAM=$this->getHeureFinInterventionAM();
//
//
//
//
//
//    if( ($HFM->format("H:i") === null ) && (  $HDM->format("H:i") !==null))
//        {
//    $context->buildViolation(' Vous Devez entrer l\'heure de fin (Matin) !')
//                ->atPath('heureDebutInterventionMatin')
//                ->addViolation();
//    }
//
//    if(($HFM->format("H:i") !== null ) && ( $HDM->format("H:i") === null))
//        {
//    $context->buildViolation(' Vous Devez entrer l\'heure de début (Matin) !')
//                ->atPath('heureDebutInterventionMatin')
//                ->addViolation();
//    }
//
//
//    if( ($HFAM->format("H:i") === null ) && ( $HDAM->format("H:i") !== null))
//        {
//    $context->buildViolation(' Vous Devez entrer l\'heure de fin (Après-Midi) !')
//                ->atPath('heureDebutInterventionMatin')
//                ->addViolation();/    }
//
//    if(($HFAM->format("H:i") !== null ) && ( $HDAM->format("H:i") === null))
//        {
//    $context->buildViolation(' Vous Devez entrer l\'heure de début (Après-Midi) !')
//                ->atPath('heureDebutInterventionMatin')
//                ->addViolation();
//    }
//
//    if($HFAM->format("H:i") < $HDAM->format("H:i"))
//        {
//    $context->buildViolation(' l\'heure de début de l\'intervention (Après-Midi) doit être supérieure à celle de fin!')
//                ->atPath('heureDebutInterventionMatin')
//                ->addViolation();
//    }
