<?php

namespace TimSoft\CommandeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * TeteCommande
 * @ORM\Table(name="tete_commande")
 * @ORM\Entity(repositoryClass="TimSoft\CommandeBundle\Repository\TeteCommandeRepository")
 * @Gedmo\Loggable
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"TeteCommande" = "TeteCommande", "PreTeteCommande" = "PreTeteCommande"})
 */
class TeteCommande implements \JsonSerializable
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
     * @ORM\Column(type="string")
     */
    protected $nCommande;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $typeArticle;
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $datePiece;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $affaire;
    /**
     * @var
     * @ORM\ManyToOne(targetEntity="TimSoft\GeneralBundle\Entity\Client", inversedBy="commandes")
     */
    protected $client;
    /**
     * @var
     * @ORM\OneToMany(targetEntity="TimSoft\CommandeBundle\Entity\LigneCommande", mappedBy="commande", cascade={"remove", "persist"})
     */
    protected $lignCommandes;
    protected $prixTotal;

    /**
     * @var
     * @ORM\ManyToOne(targetEntity="TimSoft\GeneralBundle\Entity\Utilisateur" )
     */
    protected $buManager;

    /**
     * TeteCommande constructor.
     */
    public function __construct()
    {
        $this->lignCommandes = new ArrayCollection();
        $this->prixTotal = $this->prixTotal();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getNCommande()
    {
        return $this->nCommande;
    }

    /**
     * @param mixed $nCommande
     */
    public function setNCommande($nCommande)
    {
        $this->nCommande = $nCommande;
    }

    /**
     * @return mixed
     */
    public function getDatePiece()
    {
        return $this->datePiece;
    }

    /**
     * @param mixed $datePiece
     */
    public function setDatePiece($datePiece)
    {
        $this->datePiece = $datePiece;
    }

    /**
     * @return mixed
     */
    public function getAffaire()
    {
        return $this->affaire;
    }

    /**
     * @param mixed $affaire
     */
    public function setAffaire($affaire): void
    {
        $this->affaire = $affaire;
    }


    /**
     * @return mixed
     */
    public function getTypeArticle()
    {
        return $this->typeArticle;
    }

    /**
     * @param mixed $typeArticle
     */
    public function setTypeArticle($typeArticle)
    {
        $this->typeArticle = $typeArticle;
    }

    /**
     * @return mixed
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param mixed $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * @return mixed
     */
    public function getLignCommandes()
    {
        return $this->lignCommandes;
    }

    /**
     * @param mixed $lignCommandes
     */
    public function setLignCommandes($lignCommandes)
    {
        $this->lignCommandes = $lignCommandes;
    }

    /**
     * @return mixed
     */
    public function getBuManager()
    {
        return $this->buManager;
    }

    /**
     * @param mixed $buManager
     */
    public function setBuManager($buManager): void
    {
        $this->buManager = $buManager;
    }

    public function isEmpty()
    {
        return $this->lignCommandes->isEmpty();
    }

    public function removeAll()
    {
        $this->lignCommandes->clear();
    }

    public function prixTotal()
    {
        $int = 0;
        foreach ($this->lignCommandes as $lignCommande) {
            $int = $int + $lignCommande->getMontantHT();
        }
        return $int;
    }

    public function totalRAP()
    {
        $int = 0;
        foreach ($this->lignCommandes as $lignCommande) {
            $int = $int + $lignCommande->JRestant();
        }
        return $int;
    }

    public function Bus()
    {
        $int = [];
        foreach ($this->lignCommandes as $lignCommande) {
            if (!in_array($lignCommande->getBu(), $int)) {
                $int[] = $lignCommande->getBu();
            }
        }
        return $int;
    }

    public function convert(PreTeteCommande $preTeteCommande)
    {
        $this->nCommande = $preTeteCommande->getNCommande();
        $this->datePiece = $preTeteCommande->getDatePiece();
        $this->client = $preTeteCommande->getClient();
        $this->buManager = $preTeteCommande->getBuManager();
        return $this;
    }

    public function addLigne(LigneCommande $ligneCommande)
    {
        $this->lignCommandes->add($ligneCommande);
    }

    public function __toString()
    {
        return (string)$this->nCommande->toString();
    }

    public function getbyBus($bus)
    {

        foreach ($this->lignCommandes as $lignCommande) {
            if (!in_array($lignCommande->getBu(), $bus)) {
                return $this;
            } else {
                return null;
            }
        }

    }

    public function jsonSerialize()
    {
        $return = array(
            'id' => $this->id,
            'nCommande' => $this->nCommande,
            'Client' => $this->client->getRaisonSociale(),
            'Date' => $this->datePiece->format('c'),
            'RAP' => $this->totalRAP(),
            'Bus' => $this->Bus(),
            'Lc' => $this->lignCommandes->toArray()
        );
        if ($this->buManager) {
            $return['BusinessManager'] = $this->buManager->__toString();
        } else {
            $return['BusinessManager'] = null;

        }
        return $return;
    }
}

