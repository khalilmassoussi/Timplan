<?php

namespace TimSoft\GeneralBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Referent
 *
 * @ORM\Entity(repositoryClass="TimSoft\GeneralBundle\Repository\ReferentRepository")
 * @ORM\Table(name="referent")
 *
 */
class Referent
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
     * @ORM\Column(name="NomParticipant", type="string", length=100)
     * @Assert\NotBlank
     */
    private $nomParticipant;

    /**
     * @var string
     *
     * @ORM\Column(name="PrenomParticipant", type="string", length=100)
     * @Assert\NotBlank
     */
    private $prenomParticipant;

    /**
     * @var string
     *
     * @ORM\Column(name="AdresseMailParticipant", type="string", length=200)
     * @Assert\NotBlank
     */
    private $adresseMailParticipant;

    /**
     * @ORM\ManyToOne(targetEntity="Client")
     * @ORM\JoinColumn(name="Client", referencedColumnName="id")
     */
    private $societe;

    /**
     *Plusieurs referent interviennent pour la meme intervention.
     * @ORM\ManyToOne(targetEntity="FeuilleDePresence", inversedBy="participants")
     * @ORM\JoinColumn(name="Intervention", referencedColumnName="id" , onDelete="CASCADE" )
     */
    private $intervention;

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
     * Set nomParticipant
     *
     * @param string $nomParticipant
     *
     * @return Referent
     */
    public function setNomParticipant($nomParticipant)
    {
        $this->nomParticipant = $nomParticipant;

        return $this;
    }

    /**
     * Get nomParticipant
     *
     * @return string
     */
    public function getNomParticipant()
    {
        return $this->nomParticipant;
    }

    /**
     * Set prenomParticipant
     *
     * @param string $prenomParticipant
     *
     * @return Referent
     */
    public function setPrenomParticipant($prenomParticipant)
    {
        $this->prenomParticipant = $prenomParticipant;

        return $this;
    }

    /**
     * Get prenomParticipant
     *
     * @return string
     */
    public function getPrenomParticipant()
    {
        return $this->prenomParticipant;
    }

    /**
     * Set adresseMailParticipant
     *
     * @param string $adresseMailParticipant
     *
     * @return Referent
     */
    public function setAdresseMailParticipant($adresseMailParticipant)
    {
        $this->adresseMailParticipant = $adresseMailParticipant;

        return $this;
    }

    /**
     * Get adresseMailParticipant
     *
     * @return string
     */
    public function getAdresseMailParticipant()
    {
        return $this->adresseMailParticipant;
    }

    /**
     * Set societe
     *
     * @param \TimSoft\GeneralBundle\Entity\Client $societe
     *
     * @return Referent
     */
    public function setSociete(\TimSoft\GeneralBundle\Entity\Client $societe = null)
    {
        $this->societe = $societe;

        return $this;
    }

    /**
     * Get societe
     *
     * @return \TimSoft\GeneralBundle\Entity\Client
     */
    public function getSociete()
    {
        return $this->societe;
    }

    /**
     * Set intervention
     *
     * @param \TimSoft\GeneralBundle\Entity\FeuilleDePresence $intervention
     *
     * @return Referent
     */
    public function setIntervention(\TimSoft\GeneralBundle\Entity\FeuilleDePresence $intervention)
    {
        $this->intervention = $intervention;

        return $this;
    }

    /**
     * Get intervention
     *
     * @return \TimSoft\GeneralBundle\Entity\FeuilleDePresence
     */
    public function getIntervention()
    {
        return $this->intervention;
    }
}
