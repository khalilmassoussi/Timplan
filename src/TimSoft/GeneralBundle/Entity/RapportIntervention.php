<?php

namespace TimSoft\GeneralBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * RapportIntervention
 *
 * @ORM\Table(name="rapport_intervention")
 * @ORM\Entity(repositoryClass="TimSoft\GeneralBundle\Repository\RapportInterventionRepository")
 * @Gedmo\Loggable
 */
class RapportIntervention implements \JsonSerializable
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
     * @var string
     *
     * @ORM\Column(name="TypeIntervention", type="string", length=255, nullable=true)
     */
    private $typeIntervention;

    /**
     * @var string
     *
     * @ORM\Column(name="CompteRenduIntervention", type="text", nullable=true)
     * @Assert\NotBlank()
     */
    private $compteRenduIntervention;

    /**
     * @var string
     *
     * @ORM\Column(name="RemarqueClient", type="text", nullable=true)
     */
    private $remarqueClient;

    /**
     * @var boolean
     * @Gedmo\Versioned
     * @ORM\Column(name="ConfirmationDeInterventionParClient", type="boolean", nullable=true)
     */
    private $confirmationDeInterventionParClient;

    /**
     * Un rapport est relatif à une feuille de présence
     * @ORM\OneToOne(targetEntity="FeuilleDePresence", inversedBy="rapportIntervention")
     * @ORM\JoinColumn(name="FeuilleDePresence", referencedColumnName="id", onDelete="CASCADE")
     */
    private $FeuilleDePresence;

    /**
     * Un rapport  a plusieurs fichiers join.
     * @ORM\OneToMany(targetEntity="FichierJoin", mappedBy="RapportIntervention" , cascade={"persist"})
     */
    private $fichiersJoin;
    /**
     * @var
     * @ORM\Column(type="boolean")
     */
    private $done = false;

    public function __construct()
    {
        $this->fichiersJoin = new ArrayCollection();
    }

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
     * Set typeIntervention
     *
     * @param string $typeIntervention
     *
     * @return RapportIntervention
     */
    public function setTypeIntervention($typeIntervention)
    {
        $this->typeIntervention = $typeIntervention;

        return $this;
    }

    /**
     * Get typeIntervention
     *
     * @return string
     */
    public function getTypeIntervention()
    {
        return $this->typeIntervention;
    }

    /**
     * Set compteRenduIntervention
     *
     * @param string $compteRenduIntervention
     *
     * @return RapportIntervention
     */
    public function setCompteRenduIntervention($compteRenduIntervention)
    {
        $this->compteRenduIntervention = $compteRenduIntervention;

        return $this;
    }

    /**
     * Get compteRenduIntervention
     *
     * @return string
     */
    public function getCompteRenduIntervention()
    {
        return $this->compteRenduIntervention;
    }

    /**
     * Set remarqueClient
     *
     * @param string $remarqueClient
     *
     * @return RapportIntervention
     */
    public function setRemarqueClient($remarqueClient)
    {
        $this->remarqueClient = $remarqueClient;

        return $this;
    }

    /**
     * Get remarqueClient
     *
     * @return string
     */
    public function getRemarqueClient()
    {
        return $this->remarqueClient;
    }


    /**
     * Set FeuilleDePresence
     *
     * @param \TimSoft\GeneralBundle\Entity\FeuilleDePresence $FeuilleDePresence
     *
     * @return RapportIntervention
     */
    public function setFeuilleDePresence(\TimSoft\GeneralBundle\Entity\FeuilleDePresence $FeuilleDePresence = null)
    {
        $this->FeuilleDePresence = $FeuilleDePresence;

        return $this;
    }

    /**
     * Get FeuilleDePresence
     *
     * @return \TimSoft\GeneralBundle\Entity\FeuilleDePresence
     */
    public function getFeuilleDePresence()
    {
        return $this->FeuilleDePresence;
    }

    /**
     * Add fichiersJoin
     *
     * @param \TimSoft\GeneralBundle\Entity\FichierJoin $fichiersJoin
     *
     * @return RapportIntervention
     */
    public function addFichiersJoin(\TimSoft\GeneralBundle\Entity\FichierJoin $fichiersJoin)
    {
        $fichiersJoin->setRapportIntervention($this);
        $this->fichiersJoin[] = $fichiersJoin;

        return $this;
    }

    /**
     * Remove fichiersJoin
     *
     * @param \TimSoft\GeneralBundle\Entity\FichierJoin $fichiersJoin
     */
    public function removeFichiersJoin(\TimSoft\GeneralBundle\Entity\FichierJoin $fichiersJoin)
    {
        $this->fichiersJoin->removeElement($fichiersJoin);
    }

    /**
     * Get fichiersJoin
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFichiersJoin()
    {
        return $this->fichiersJoin;
    }

    /**
     * Set confirmationDeInterventionParClient
     *
     * @param boolean $confirmationDeInterventionParClient
     *
     * @return RapportIntervention
     */
    public function setConfirmationDeInterventionParClient($confirmationDeInterventionParClient)
    {
        $this->confirmationDeInterventionParClient = $confirmationDeInterventionParClient;

        return $this;
    }

    /**
     * Get confirmationDeInterventionParClient
     *
     * @return boolean
     */
    public function getConfirmationDeInterventionParClient()
    {
        return $this->confirmationDeInterventionParClient;
    }

    public function __toString()
    {
        return (string)$this->getFeuilleDePresence()->getClient() . ' - ' . $this->getFeuilleDePresence()->getIntervenant() . ' le : ' . $this->getFeuilleDePresence()->getDateIntervention()->format('d-m-Y');
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


    public function jsonSerialize()
    {
        $array = array(
            'id' => $this->id,
            'Date' => $this->FeuilleDePresence->getDateIntervention()->format('d-m-Y'),
            'Client' => $this->FeuilleDePresence->getClient()->getRaisonSociale(),
            'Intervenant' => $this->FeuilleDePresence->getIntervenant()->__toString(),
            'Type' => "Rapport d'intervention",
        );
        if ($this->confirmationDeInterventionParClient) {
            $array['Validation'] = 'Validé';
        } else {
            $array['Validation'] = 'Non Validé';
        }
        return $array;
    }
}
