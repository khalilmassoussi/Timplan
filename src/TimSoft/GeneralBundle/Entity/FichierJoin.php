<?php

namespace TimSoft\GeneralBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * FichierJoin
 *
 * @ORM\Table(name="fichier_join")
 * @ORM\Entity(repositoryClass="TimSoft\GeneralBundle\Repository\FichierJoinRepository")
 * @Vich\Uploadable
 */
class FichierJoin
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
     * @ORM\Column(name="TypeFichier", type="string", length=255, nullable=true)
     */
    private $typeFichier;

    /**
     * @var string
     *
     * @ORM\Column(name="NomFichierJoin", type="string", length=255)
     *  @Assert\NotBlank()
     * @Assert\Length(min=3, minMessage="Le nom de fichier doit au moins contenir 3 caractères !")
     * @Assert\Length(min=255, maxMessage="Le nom de fichier doit au plus contenir 255 caractères !")
     */
    private $nomFichierJoin;

    /**
     * @ORM\Column(name="ContenuFichierJoin", type="string")
     * * @var string
     */
    private $contenuFichierJoin;
    
     /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * 
     * @Vich\UploadableField(mapping="UploadFichierJoin", fileNameProperty="contenuFichierJoin")
     * 
     * @var File
     */
    private $fichierFile;
    
     /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $updatedAt;

     /**
     * @var boolean
     *
     * @ORM\Column(name="SendParMail", type="boolean")
     */
    private $sendParMail;
    
     /**
     * Plusieurs fichiers sont relatifs à un rapport
     * @ORM\ManyToOne(targetEntity="RapportIntervention", inversedBy="fichiersJoin")
     * @ORM\JoinColumn(name="RapportIntervention", referencedColumnName="id", onDelete="CASCADE")
     */
    private $RapportIntervention;

    
    public function setFichierFile(File $contenuFichierJoin = null)
    {
        $this->fichierFile = $contenuFichierJoin;

        
        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($contenuFichierJoin) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getFichierFile()
    {
        return $this->fichierFile;
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
     * Set typeFichier
     *
     * @param string $typeFichier
     *
     * @return FichierJoin
     */
    public function setTypeFichier($typeFichier)
    {
        $this->typeFichier = $typeFichier;

        return $this;
    }

    /**
     * Get typeFichier
     *
     * @return string
     */
    public function getTypeFichier()
    {
        return $this->typeFichier;
    }

    /**
     * Set nomFichierJoin
     *
     * @param string $nomFichierJoin
     *
     * @return FichierJoin
     */
    public function setNomFichierJoin($nomFichierJoin)
    {
        $this->nomFichierJoin = $nomFichierJoin;

        return $this;
    }

    /**
     * Get nomFichierJoin
     *
     * @return string
     */
    public function getNomFichierJoin()
    {
        return $this->nomFichierJoin;
    }

    /**
     * Set contenuFichierJoin
     *
     * @param string $contenuFichierJoin
     *
     * @return FichierJoin
     */
    public function setContenuFichierJoin($contenuFichierJoin)
    {
        $this->contenuFichierJoin = $contenuFichierJoin;

        return $this;
    }

    /**
     * Get contenuFichierJoin
     *
     * @return string
     */
    public function getContenuFichierJoin()
    {
        return $this->contenuFichierJoin;
    }

    /**
     * Set sendParMail
     *
     * @param boolean $sendParMail
     *
     * @return FichierJoin
     */
    public function setSendParMail($sendParMail)
    {
        $this->sendParMail = $sendParMail;

        return $this;
    }

    /**
     * Get sendParMail
     *
     * @return boolean
     */
    public function getSendParMail()
    {
        return $this->sendParMail;
    }

    /**
     * Set rapportIntervention
     *
     * @param \TimSoft\GeneralBundle\Entity\RapportIntervention $rapportIntervention
     *
     * @return FichierJoin
     */
    public function setRapportIntervention(\TimSoft\GeneralBundle\Entity\RapportIntervention $rapportIntervention = null)
    {
        $this->RapportIntervention = $rapportIntervention;

        return $this;
    }

    /**
     * Get rapportIntervention
     *
     * @return \TimSoft\GeneralBundle\Entity\RapportIntervention
     */
    public function getRapportIntervention()
    {
        return $this->RapportIntervention;
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
}
