<?php

namespace TimSoft\GeneralBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Client
 *
 * @ORM\Table(name="client")
 * @ORM\Entity(repositoryClass="TimSoft\GeneralBundle\Repository\ClientRepository")
 */
class Client
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
     * @var int
     * @ORM\Column(name="CodeClient", type="integer", unique=false, nullable=true)
     */
    private $codeClient;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="clientFacture")
     */
    protected $societeMere;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Client", mappedBy="societeMere", cascade={"persist" , "remove"})
     */
    private $clientFacture;


    /**
     * @var string
     * @Assert\NotBlank(message="Il faut introduire la raison sociale")
     * @Assert\Length(min=3, minMessage="Le nom de la société doit au moins contenir 3 caractères !")
     * @ORM\Column(name="RaisonSociale", type="string", length=255, unique=false)
     */
    private $raisonSociale;


    /**
     * @var string
     *
     * @ORM\Column(name="PaysClient", type="string", length=255, nullable=true)
     */
    private $paysClient;

    /**
     * @var string
     * @ORM\Column(name="AdresseClient", type="string", length=255, nullable=true)
     */
    private $adresseClient;

    /**
     * @var string
     * @ORM\Column(name="VilleClient", type="string", length=255 , nullable=true)
     */
    private $villeClient;

    /**
     * @var int
     * @ORM\Column(name="CodePostalClient", type="integer", nullable=true)
     */
    private $codePostalClient;

    /**
     * @var string
     * @ORM\Column(name="Telephone", type="string", length=30, nullable=true)
     */
    private $telephone;

    /**
     * @var string
     */
    private $faxClient;

    /**
     * @var string
     * @ORM\Column(name="AdresseMailClient", type="string", length=100 ,nullable=true)
     */
    private $adresseMailClient;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DateAdhesionClient", type="date")
     */
    private $dateAdhesionClient;
    /**
     * @var
     * @ORM\OneToMany(targetEntity="TimSoft\CommandeBundle\Entity\TeteCommande", mappedBy="client")
     */
    private $commandes;

    /**
     * @var
     * @ORM\OneToMany(targetEntity="TimSoft\TasksBundle\Entity\TaskEvent", mappedBy="client")
     */
    private $tasks;

    /**
     * @var
     * @ORM\OneToMany(targetEntity="TimSoft\GeneralBundle\Entity\Affaire", mappedBy="client")
     */
    private $affaires;

    /**
     * @var
     * @ORM\Column(type="boolean")
     */
    private $bloque;

    /**
     * @var
     * @ORM\OneToMany(targetEntity="TimSoft\CommandeBundle\Entity\PreTeteCommande", mappedBy="client")
     */
    private $precommandes;

    public function __toString()
    {
        return (string)$this->getRaisonSociale();
    }

    public function __construct()
    {
        $this->dateAdhesionClient = new \DateTime('now');
        $this->clientFacture = new ArrayCollection();
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
     * Set codeClient
     *
     * @param integer $codeClient
     *
     * @return Client
     */
    public function setCodeClient($codeClient)
    {
        $this->codeClient = $codeClient;

        return $this;
    }

    /**
     * Get codeClient
     *
     * @return integer
     */
    public function getCodeClient()
    {
        return $this->codeClient;
    }

    /**
     * Set raisonSociale
     *
     * @param string $raisonSociale
     *
     * @return Client
     */
    public function setRaisonSociale($raisonSociale)
    {
        $this->raisonSociale = $raisonSociale;

        return $this;
    }

    /**
     * Get raisonSociale
     *
     * @return string
     */
    public function getRaisonSociale()
    {
        return $this->raisonSociale;
    }

    /**
     * Set paysClient
     *
     * @param string $paysClient
     *
     * @return Client
     */
    public function setPaysClient($paysClient)
    {
        $this->paysClient = $paysClient;

        return $this;
    }

    /**
     * Get paysClient
     *
     * @return string
     */
    public function getPaysClient()
    {
        return $this->paysClient;
    }

    /**
     * Set adresseClient
     *
     * @param string $adresseClient
     *
     * @return Client
     */
    public function setAdresseClient($adresseClient)
    {
        $this->adresseClient = $adresseClient;

        return $this;
    }

    /**
     * Get adresseClient
     *
     * @return string
     */
    public function getAdresseClient()
    {
        return $this->adresseClient;
    }

    /**
     * Set villeClient
     *
     * @param string $villeClient
     *
     * @return Client
     */
    public function setVilleClient($villeClient)
    {
        $this->villeClient = $villeClient;

        return $this;
    }

    /**
     * Get villeClient
     *
     * @return string
     */
    public function getVilleClient()
    {
        return $this->villeClient;
    }

    /**
     * Set codePostalClient
     *
     * @param integer $codePostalClient
     *
     * @return Client
     */
    public function setCodePostalClient($codePostalClient)
    {
        $this->codePostalClient = $codePostalClient;

        return $this;
    }

    /**
     * Get codePostalClient
     *
     * @return integer
     */
    public function getCodePostalClient()
    {
        return $this->codePostalClient;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     *
     * @return Client
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set faxClient
     *
     * @param string $faxClient
     *
     * @return Client
     */
    public function setFaxClient($faxClient)
    {
        $this->faxClient = $faxClient;

        return $this;
    }

    /**
     * Get faxClient
     *
     * @return string
     */
    public function getFaxClient()
    {
        return $this->faxClient;
    }

    /**
     * Set adresseMailClient
     *
     * @param string $adresseMailClient
     *
     * @return Client
     */
    public function setAdresseMailClient($adresseMailClient)
    {
        $this->adresseMailClient = $adresseMailClient;

        return $this;
    }

    /**
     * Get adresseMailClient
     *
     * @return string
     */
    public function getAdresseMailClient()
    {
        return $this->adresseMailClient;
    }

    /**
     * Set dateAdhesionClient
     *
     * @param \DateTime $dateAdhesionClient
     *
     * @return Client
     */
    public function setDateAdhesionClient($dateAdhesionClient)
    {
        $this->dateAdhesionClient = $dateAdhesionClient;

        return $this;
    }

    /**
     * Get dateAdhesionClient
     *
     * @return \DateTime
     */
    public function getDateAdhesionClient()
    {
        return $this->dateAdhesionClient;
    }

    /**
     * Set societeMere
     *
     * @param \TimSoft\GeneralBundle\Entity\Client $societeMere
     *
     * @return Client
     */
    public function setSocieteMere(\TimSoft\GeneralBundle\Entity\Client $societeMere = null)
    {
        $this->societeMere = $societeMere;

        return $this;
    }

    /**
     * Get societeMere
     *
     * @return \TimSoft\GeneralBundle\Entity\Client
     */
    public function getSocieteMere()
    {
        return $this->societeMere;
    }

    /**
     * Add clientFacture
     *
     * @param \TimSoft\GeneralBundle\Entity\Client $clientFacture
     *
     * @return Client
     */
    public function addClientFacture(\TimSoft\GeneralBundle\Entity\Client $clientFacture)
    {
        $this->clientFacture[] = $clientFacture;

        return $this;
    }

    /**
     * Remove clientFacture
     *
     * @param \TimSoft\GeneralBundle\Entity\Client $clientFacture
     */
    public function removeClientFacture(\TimSoft\GeneralBundle\Entity\Client $clientFacture)
    {
        $this->clientFacture->removeElement($clientFacture);
    }

    /**
     * Get clientFacture
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getClientFacture()
    {
        return $this->clientFacture;
    }

    /**
     * @return mixed
     */
    public function getCommandes()
    {
        return $this->commandes;
    }

    /**
     * @param mixed $commandes
     */
    public function setCommandes($commandes)
    {
        $this->commandes = $commandes;
    }

    /**
     * @return mixed
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    /**
     * @param mixed $tasks
     */
    public function setTasks($tasks): void
    {
        $this->tasks = $tasks;
    }

    /**
     * @return mixed
     */
    public function getAffaires()
    {
        return $this->affaires;
    }

    /**
     * @param mixed $affaires
     */
    public function setAffaires($affaires): void
    {
        $this->affaires = $affaires;
    }

    /**
     * @return mixed
     */
    public function getBloque()
    {
        return $this->bloque;
    }

    /**
     * @param mixed $bloque
     */
    public function setBloque($bloque): void
    {
        $this->bloque = $bloque;
    }

    /**
     * @return mixed
     */
    public function getPrecommandes()
    {
        return $this->precommandes;
    }

    /**
     * @param mixed $precommandes
     */
    public function setPrecommandes($precommandes): void
    {
        $this->precommandes = $precommandes;
    }



}
