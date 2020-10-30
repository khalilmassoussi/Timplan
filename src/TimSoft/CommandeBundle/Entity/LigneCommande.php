<?php

namespace TimSoft\CommandeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * LigneCommande
 *
 * @ORM\Table(name="ligne_commande")
 * @ORM\Entity(repositoryClass="TimSoft\CommandeBundle\Repository\LigneCommandeRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="d", type="string")
 * @ORM\DiscriminatorMap({"LigneCommande" = "LigneCommande", "PreLigneCommande" = "PreLigneCommande"})
 */
class LigneCommande implements \JsonSerializable
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
     * @ORM\Column(type="string")
     */
    /**
     * @var
     * @ORM\Column(type="integer")
     */
    private $nLigne;
    /**
     * @var
     * @ORM\Column(type="string")
     */
    private $type;
    /**
     * @var
     * @ORM\Column(type="string")
     */
    private $libelle;
    /**
     * @ORM\Column(type="float")
     */
    private $quantite;
    /**
     * @ORM\Column(type="float")
     */
    private $montantHT;
    /**
     * @ORM\Column(type="float" , nullable=true)
     */
    private $prixUnitaire;
    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $prixTotal;
    /**
     * @ORM\Column(type="float")
     */
    private $QteRestante;
    /**
     * @var
     * @ORM\ManyToOne(targetEntity="TimSoft\CommandeBundle\Entity\TeteCommande", inversedBy="lignCommandes", cascade={"persist"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $commande;
    /**
     * @var
     * @ORM\Column(type="string", nullable=true)
     */
    private $bu;
    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $echec = false;
    private $prixDeVente;
    /**
     * @var
     * @ORM\OneToMany(targetEntity="TimSoft\GeneralBundle\Entity\Planning", mappedBy="lc", cascade={"remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $plannings;


    /**
     * LigneCommande constructor.
     * @param $prixDeVente
     */
    public function __construct()
    {
        $this->prixDeVente = $this->calcul();
        $this->plannings = new ArrayCollection();
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
    public function getQuantite()
    {
        return $this->quantite;
    }

    /**
     * @param mixed $quantite
     */
    public function setQuantite($quantite)
    {
        $this->quantite = $quantite;
    }

    /**
     * @return mixed
     */
    public function getPrixUnitaire()
    {
        return $this->prixUnitaire;
    }

    /**
     * @param mixed $prixUnitaire
     */
    public function setPrixUnitaire($prixUnitaire)
    {
        $this->prixUnitaire = $prixUnitaire;
    }

    /**
     * @return mixed
     */
    public function getPrixTotal()
    {
        return $this->prixTotal;
    }

    /**
     * @param mixed $prixTotal
     */
    public function setPrixTotal($prixTotal)
    {
        $this->prixTotal = $prixTotal;
    }

    /**
     * @return mixed
     */
    public function getCommande()
    {
        return $this->commande;
    }

    /**
     * @param mixed $commande
     */
    public function setCommande($commande)
    {
        $this->commande = $commande;
    }

    /**
     * @return mixed
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * @param mixed $libelle
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;
    }

    /**
     * @return mixed
     */
    public function getMontantHT()
    {
        return $this->montantHT;
    }

    /**
     * @param mixed $montantHT
     */
    public function setMontantHT($montantHT)
    {
        $this->montantHT = $montantHT;
    }

    /**
     * @return mixed
     */
    public function getBu()
    {
        return $this->bu;
    }

    /**
     * @param mixed $bu
     */
    public function setBu($bu)
    {
        $this->bu = $bu;
    }

    /**
     * @return mixed
     */
    public function getNLigne()
    {
        return $this->nLigne;
    }

    /**
     * @param mixed $nLigne
     */
    public function setNLigne($nLigne)
    {
        $this->nLigne = $nLigne;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getQteRestante()
    {
        return $this->QteRestante;
    }

    /**
     * @param mixed $QteRestante
     */
    public function setQteRestante($QteRestante)
    {
        $this->QteRestante = $QteRestante;
    }

    /**
     * @return bool
     */
    public function isEchec()
    {
        return $this->echec;
    }

    /**
     * @param bool $echec
     */
    public function setEchec($echec)
    {
        $this->echec = $echec;
    }

    /**
     * @return mixed
     */
    public function getPrixDeVente()
    {
        return $this->prixDeVente;
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

    /**
     * @param mixed $prixDeVente
     */
    public function setPrixDeVente($prixDeVente)
    {
        $this->prixDeVente = $prixDeVente;
    }


    public function calcul()
    {
        if ($this->montantHT != 0 && $this->quantite != 0)
            return $this->montantHT / $this->quantite;
    }

    public function JRestant()
    {
        $montant = 0;
        foreach ($this->getPlannings() as $planning) {
            if ($planning->getStatut() != 'Terminé') {
                $montant = $montant + ($planning->jRestantes());
            }
        }
        return $this->QteRestante - $montant;
    }

    public function MRestant()
    {
        return $this->QteRestante * $this->calcul();
    }

    public function nbJours()
    {
        $nbjours = 0;
        foreach ($this->getPlannings() as $planning) {
            $nbjours = $nbjours + ($planning->jRestantes());
        }
        return $nbjours;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return array(
            'id' => $this->id,
            'libelle' => $this->libelle,
            'quantite' => $this->quantite,
            'montant' => $this->montantHT,
            'qrestante' => $this->QteRestante,
            'bu' => $this->bu,
        );


    }

    public function convert(PreLigneCommande $preLigneCommande)
    {
        $this->libelle = $preLigneCommande->getLibelle();
        $this->nLigne = $preLigneCommande->getNLigne();
        $this->type = $preLigneCommande->getType();
        $this->quantite = $preLigneCommande->getQuantite();
        $this->montantHT = $preLigneCommande->getMontantHT();
        $this->QteRestante = $preLigneCommande->getQteRestante();
        $this->bu = $preLigneCommande->getBu();
        $this->type = $preLigneCommande->getType();

        return $this;
    }

    public function __toString()
    {
        return strval($this->libelle);
    }

}

