<?php

namespace TimSoft\GeneralBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as FosUser;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


/**
 * Utilisateur
 *
 * @ORM\Table(name="utilisateur")
 * @ORM\Entity(repositoryClass="TimSoft\GeneralBundle\Repository\UtilisateurRepository")
 * @Vich\Uploadable
 */
class Utilisateur extends FosUser implements \JsonSerializable
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="NomUtilisateur", type="string", length=255)
     * @Assert\Length(min=3, minMessage="Le nom de l'utilisateur doit au moins contenir 3 caractères !")
     * @Assert\Length(max=255, maxMessage="Le nom de l'utilisateur doit au plus contenir 255 caractères !")
     */
    private $nomUtilisateur;

    /**
     * @var string
     *
     * @ORM\Column(name="PrenomUtilisateur", type="string", length=255)
     * @Assert\Length(min=3, minMessage="Le prénom de l'utilisateur doit au moins contenir 3 caractères !")
     * @Assert\Length(max=255, maxMessage="Le prénom de l'utilisateur doit au plus contenir 255 caractères !")
     *
     */
    private $prenomUtilisateur;

    /**
     * @var string
     *
     * @ORM\Column(name="TelephoneUtilisateur", type="string", length=255, nullable=true)
     * @ORM\Column(name="Telephone", type="string", length=30, nullable=true)
     */
    private $telephoneUtilisateur;


    /**
     * un utilisateur  appartient à un client
     * @ORM\ManyToOne(targetEntity="Client")
     * @ORM\JoinColumn(name="Societe", referencedColumnName="id",onDelete="CASCADE")
     * @Assert\NotBlank()
     */
    private $client;


    /**
     * @var string
     *
     * @ORM\Column(name="RoleUtilisateur", type="string", length=30)
     */
    private $roleUtilisateur;

    /**
     * @var bool
     *
     * @ORM\Column(name="StatutProfil", type="boolean")
     */
    public $statutProfil;

    /**
     * @ORM\Column(name="PhotoDeProfil" , type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $photoDeProfil;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="UploadPhotoDeProfil", fileNameProperty="photoDeProfil")
     *
     * @var File
     */
    private $photoDeProfilFile;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $updatedAt;
    /**
     * @var
     */
    protected $enabled;

    /**
     * @var string
     * @ORM\Column(name="ConfirmationModifMail", type="string", nullable=true)
     * @Assert\Email(
     *     message = "L'adresse '{{ value }}' n'est pas valide.",
     * )
     */
    private $confirmationModifMail;

    /**
     * Un utilisateur a plusieurs NotificationUtilisateur.
     * @ORM\OneToMany(targetEntity="TimSoft\GeneralBundle\Entity\NotificationUtilisateur", mappedBy="Utilisateur" , cascade={"persist"})
     */
    private $NotificationUtilisateur;
    /**
     * @var
     * @ORM\ManyToMany(targetEntity="TimSoft\BuBundle\Entity\Bu", inversedBy="utilisateurs")
     */
    private $bus;
    /**
     * @var
     * @ORM\OneToMany(targetEntity="TimSoft\GeneralBundle\Entity\Planning", mappedBy="utilisateur")
     */
    private $plannings;


    /**
     * Utilisateur constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->bus = new ArrayCollection();
        $this->plannings = new ArrayCollection();
    }

    /**
     * Set nomUtilisateur
     *
     * @param string $nomUtilisateur
     *
     * @return Utilisateur
     */
    public function setNomUtilisateur($nomUtilisateur)
    {
        $this->nomUtilisateur = $nomUtilisateur;

        return $this;
    }

    /**
     * Get nomUtilisateur
     *
     * @return string
     */
    public function getNomUtilisateur()
    {
        return $this->nomUtilisateur;
    }

    /**
     * Set prenomUtilisateur
     *
     * @param string $prenomUtilisateur
     *
     * @return Utilisateur
     */
    public function setPrenomUtilisateur($prenomUtilisateur)
    {
        $this->prenomUtilisateur = $prenomUtilisateur;

        return $this;
    }

    /**
     * Get prenomUtilisateur
     *
     * @return string
     */
    public function getPrenomUtilisateur()
    {
        return $this->prenomUtilisateur;
    }

    /**
     * Set telephoneUtilisateur
     *
     * @param string $telephoneUtilisateur
     *
     * @return Utilisateur
     */
    public function setTelephoneUtilisateur($telephoneUtilisateur)
    {
        $this->telephoneUtilisateur = $telephoneUtilisateur;

        return $this;
    }

    /**
     * Get telephoneUtilisateur
     *
     * @return string
     */
    public function getTelephoneUtilisateur()
    {
        return $this->telephoneUtilisateur;
    }

    /**
     * Set client
     *
     * @param \TimSoft\GeneralBundle\Entity\Client $client
     *
     * @return Utilisateur
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


    public function __toString()
    {
        return (string)$this->getPrenomUtilisateur() . ' ' . $this->getNomUtilisateur();
    }

    /**
     * Set roleUtilisateur
     *
     * @param string $roleUtilisateur
     *
     * @return Utilisateur
     */
    public function setRoleUtilisateur($roleUtilisateur)
    {
        $this->roleUtilisateur = $roleUtilisateur;

        return $this;
    }

    /**
     * Get roleUtilisateur
     *
     * @return string
     */
    public function getRoleUtilisateur()
    {
        return $this->roleUtilisateur;
    }

    /**
     * Set statutProfil
     *
     * @param boolean $statutProfil
     *
     * @return Utilisateur
     */
    public function setStatutProfil($statutProfil)
    {
        $this->statutProfil = $statutProfil;

        return $this;
    }

    /**
     * Get statutProfil
     *
     * @return boolean
     */
    public function getStatutProfil()
    {
        return $this->statutProfil;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }


    public function jsonSerialize()
    {
        $call = [
            'id' => $this->id,
            'prenomUtilisateur' => $this->prenomUtilisateur,
            'nomUtilisateur' => $this->nomUtilisateur,
            'bus' => $this->bus->toArray(),
            'roleUtilisateur' => $this->roleUtilisateur,
            'title' => $this->nomUtilisateur . ' ' . $this->prenomUtilisateur,
            'enabled' => $this->enabled,
            'statut' => $this->statutProfil
            // 'plannings' => $this->plannings->toArray()
        ];
        return $call;
    }


    /**
     * Set photoDeProfil
     *
     * @param string $photoDeProfil
     *
     * @return Utilisateur
     */
    public function setPhotoDeProfil($photoDeProfil)
    {
        $this->photoDeProfil = $photoDeProfil;

        return $this;
    }

    /**
     * Get photoDeProfil
     *
     * @return string
     */
    public function getPhotoDeProfil()
    {
        return $this->photoDeProfil;
    }

    public function setPhotoDeProfilFile(File $photoDeProfil = null)
    {
        $this->photoDeProfilFile = $photoDeProfil;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($photoDeProfil) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getPhotoDeProfilFile()
    {
        return $this->photoDeProfilFile;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Utilisateur
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

    /**
     * Set confirmationModifMail
     *
     * @param string $confirmationModifMail
     *
     * @return Utilisateur
     */
    public function setConfirmationModifMail($confirmationModifMail)
    {
        $this->confirmationModifMail = $confirmationModifMail;

        return $this;
    }

    /**
     * Get confirmationModifMail
     *
     * @return string
     */
    public function getConfirmationModifMail()
    {
        return $this->confirmationModifMail;
    }

    /**
     * Add notificationUtilisateur
     *
     * @param \TimSoft\GeneralBundle\Entity\NotificationUtilisateur $notificationUtilisateur
     *
     * @return Utilisateur
     */
    public function addNotificationUtilisateur(\TimSoft\GeneralBundle\Entity\NotificationUtilisateur $notificationUtilisateur)
    {
        $this->NotificationUtilisateur[] = $notificationUtilisateur;

        return $this;
    }

    /**
     * Remove notificationUtilisateur
     *
     * @param \TimSoft\GeneralBundle\Entity\NotificationUtilisateur $notificationUtilisateur
     */
    public function removeNotificationUtilisateur(\TimSoft\GeneralBundle\Entity\NotificationUtilisateur $notificationUtilisateur)
    {
        $this->NotificationUtilisateur->removeElement($notificationUtilisateur);
    }

    /**
     * Get notificationUtilisateur
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotificationUtilisateur()
    {
        return $this->NotificationUtilisateur;
    }

    /**
     * @return mixed
     */
    public function getBus()
    {
        return $this->bus;
    }

    /**
     * @param mixed $bu
     */
    public function setBus($bus)
    {
        $this->bus = $bus;
    }

    public function removeBu($bu)
    {
        $this->bus->removeElement($bu);
    }

    public function addBu($bu)
    {
        $this->bus->add($bu);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getPlannings()
    {
        return $this->plannings;
    }

    /**
     * @param mixed $plannings
     */
    public function setPlannings($plannings)
    {
        $this->plannings = $plannings;
    }

    public function contains($bu)
    {
        return $this->bus->contains($bu);
    }
}
