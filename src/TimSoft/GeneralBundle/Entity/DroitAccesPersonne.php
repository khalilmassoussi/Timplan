<?php

namespace TimSoft\GeneralBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DroitAccesPersonne
 *
 * @ORM\Table(name="droit_acces_personne")
 * @ORM\Entity(repositoryClass="TimSoft\GeneralBundle\Repository\DroitAccesPersonneRepository")
 */
class DroitAccesPersonne
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
     * ID du droit
     * @ORM\ManyToOne(targetEntity="DroitAcces")
     * @ORM\JoinColumn(name="Droit", referencedColumnName="id")
     */
    private $droit;

    /**
     * ID Personne
     * @ORM\ManyToOne(targetEntity="Utilisateur")
     * @ORM\JoinColumn(name="Utilisateur", referencedColumnName="id", onDelete="CASCADE")
     */
    private $utilisateur;

    /**
     * @var string
     *
     * @ORM\Column(name="RoleUtilisateur", type="string", length=30)
     */
    private $roleUtilisateur;

    /**
     * @var bool
     *
     * @ORM\Column(name="Autorisation", type="boolean")
     */
    private $autorisation;


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
     * Set roleUtilisateur
     *
     * @param string $roleUtilisateur
     *
     * @return DroitAccesPersonne
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
     * Set autorisation
     *
     * @param boolean $autorisation
     *
     * @return DroitAccesPersonne
     */
    public function setAutorisation($autorisation)
    {
        $this->autorisation = $autorisation;

        return $this;
    }

    /**
     * Get autorisation
     *
     * @return boolean
     */
    public function getAutorisation()
    {
        return $this->autorisation;
    }

    /**
     * Set droit
     *
     * @param \TimSoft\GeneralBundle\Entity\DroitAcces $droit
     *
     * @return DroitAccesPersonne
     */
    public function setDroit(\TimSoft\GeneralBundle\Entity\DroitAcces $droit = null)
    {
        $this->droit = $droit;

        return $this;
    }

    /**
     * Get droit
     *
     * @return \TimSoft\GeneralBundle\Entity\DroitAcces
     */
    public function getDroit()
    {
        return $this->droit;
    }

    /**
     * Set utilisateur
     *
     * @param \TimSoft\GeneralBundle\Entity\Utilisateur $utilisateur
     *
     * @return DroitAccesPersonne
     */
    public function setUtilisateur(\TimSoft\GeneralBundle\Entity\Utilisateur $utilisateur = null)
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * Get utilisateur
     *
     * @return \TimSoft\GeneralBundle\Entity\Utilisateur
     */
    public function getUtilisateur()
    {
        return $this->utilisateur;
    }
}
