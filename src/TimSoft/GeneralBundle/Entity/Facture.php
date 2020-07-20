<?php

namespace TimSoft\GeneralBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Facture
 *
 * @ORM\Table(name="facture")
 * @ORM\Entity(repositoryClass="TimSoft\GeneralBundle\Repository\FactureRepository")
 * @Vich\Uploadable
 */
class Facture
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
     * @ORM\Column(name="NumeroFacture", type="integer", unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(min=3, minMessage="Le numéro de la facture doit au moins contenir 3 chiffres !")
     * @Assert\Length(max=9, maxMessage="Le numéro de la facture doit au plus contenir 9 chiffres !")
     * @Assert\GreaterThan(0)
     */
    private $numeroFacture;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DateFacture", type="date")
     */
    private $dateFacture;

    /**
     * @var string
     *
     * @ORM\Column(name="NatureFacture", type="string", length=20)
     */
    private $natureFacture;

    /**
     * @var string
     *
     * @ORM\Column(name="StatutFacture", type="string", length=50)
     */
    private $statutFacture;

    /**
     * @var string
     *
     * @ORM\Column(name="CauseRefusFacture",type="text", nullable=true)
     */
    private $causeRefusFacture;

    /**
     * @var string
     *
     * @ORM\Column(name="MontantTTCFacture", type="decimal", precision=18, scale=3)
     */
    private $montantTTCFacture;

    /**
     * @ORM\Column(name="FacturePDF" , type="string", length=255)
     *
     * @var string
     */
    private $facturePDF;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="UploadFacture", fileNameProperty="facturePDF")
     *
     * @var File
     */
    private $factureFile;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $updatedAt;



    /**
     *
     * un utilisateur  appartient à un client
     * @ORM\ManyToOne(targetEntity="Client")
     * @ORM\JoinColumn(name="Societe", referencedColumnName="id" ,onDelete="CASCADE")
     */
    protected $client;


    public function __construct()
    {
        $this->dateFacture = new \DateTime('now');
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
     * Set numeroFacture
     *
     * @param integer $numeroFacture
     *
     * @return Facture
     */
    public function setNumeroFacture($numeroFacture)
    {
        $this->numeroFacture = $numeroFacture;

        return $this;
    }

    /**
     * Get numeroFacture
     *
     * @return integer
     */
    public function getNumeroFacture()
    {
        return $this->numeroFacture;
    }

    /**
     * Set dateFacture
     *
     * @param \DateTime $dateFacture
     *
     * @return Facture
     */
    public function setDateFacture($dateFacture)
    {
        $this->dateFacture = $dateFacture;

        return $this;
    }

    /**
     * Get dateFacture
     *
     * @return \DateTime
     */
    public function getDateFacture()
    {
        return $this->dateFacture;
    }

    /**
     * Set natureFacture
     *
     * @param string $natureFacture
     *
     * @return Facture
     */
    public function setNatureFacture($natureFacture)
    {
        $this->natureFacture = $natureFacture;

        return $this;
    }

    /**
     * Get natureFacture
     *
     * @return string
     */
    public function getNatureFacture()
    {
        return $this->natureFacture;
    }

    /**
     * Set montantTTCFacture
     *
     * @param string $montantTTCFacture
     *
     * @return Facture
     */
    public function setMontantTTCFacture($montantTTCFacture)
    {
        $this->montantTTCFacture = $montantTTCFacture;

        return $this;
    }

    /**
     * Get montantTTCFacture
     *
     * @return string
     */
    public function getMontantTTCFacture()
    {
        return $this->montantTTCFacture;
    }

    public function setFactureFile(File $facturePDF = null)
    {
        $this->factureFile = $facturePDF;


        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($facturePDF) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getFactureFile()
    {
        return $this->factureFile;
    }

    /**
     * Set client
     *
     * @param \TimSoft\GeneralBundle\Entity\Client $client
     *
     * @return Facture
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
     * Set statutFacture
     *
     * @param string $statutFacture
     *
     * @return Facture
     */
    public function setStatutFacture($statutFacture)
    {
        $this->statutFacture = $statutFacture;

        return $this;
    }

    /**
     * Get statutFacture
     *
     * @return string
     */
    public function getStatutFacture()
    {
        return $this->statutFacture;
    }

    /**
     * Set causeRefusFacture
     *
     * @param string $causeRefusFacture
     *
     * @return Facture
     */
    public function setCauseRefusFacture($causeRefusFacture)
    {
        $this->causeRefusFacture = $causeRefusFacture;

        return $this;
    }

    /**
     * Get causeRefusFacture
     *
     * @return string
     */
    public function getCauseRefusFacture()
    {
        return $this->causeRefusFacture;
    }



    /**
     * Set facturePDF
     *
     * @param string $facturePDF
     *
     * @return Facture
     */
    public function setFacturePDF($facturePDF)
    {
        $this->facturePDF = $facturePDF;

        return $this;
    }

    /**
     * Get facturePDF
     *
     * @return string
     */
    public function getFacturePDF()
    {
        return $this->facturePDF;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Facture
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function __toString()
    {
        return (string) 'Facture N°'.$this->getNumeroFacture().' - Société: '.$this->getClient().' - Date : '.$this->getDateFacture()->format('d-m-Y');
    }
}
