<?php

namespace TimSoft\GeneralBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * DroitAcces
 *
 * @ORM\Table(name="droit_acces")
 * @ORM\Entity(repositoryClass="TimSoft\GeneralBundle\Repository\DroitAccesRepository")
 */
class DroitAcces
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
     * @ORM\Column(name="NomDroit", type="string", length=100, unique=true)
     */
    private $nomDroit;

    /**
     * @var string
     *
     * @ORM\Column(name="RouteDroit", type="string", length=255, unique=true)
     */
    private $routeDroit;

    /**
     * @var string
     *
     * @ORM\Column(name="CategorieDroit", type="string", length=100)
     */
    private $categorieDroit;

    /**
     * @var
     * @ORM\Column(type="boolean")
     */
    private $technique;

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
     * Set nomDroit
     *
     * @param string $nomDroit
     *
     * @return DroitAcces
     */
    public function setNomDroit($nomDroit)
    {
        $this->nomDroit = $nomDroit;

        return $this;
    }

    /**
     * Get nomDroit
     *
     * @return string
     */
    public function getNomDroit()
    {
        return $this->nomDroit;
    }

    /**
     * Set routeDroit
     *
     * @param string $routeDroit
     *
     * @return DroitAcces
     */
    public function setRouteDroit($routeDroit)
    {
        $this->routeDroit = $routeDroit;

        return $this;
    }

    /**
     * Get routeDroit
     *
     * @return string
     */
    public function getRouteDroit()
    {
        return $this->routeDroit;
    }

    /**
     * Set categorieDroit
     *
     * @param string $categorieDroit
     *
     * @return DroitAcces
     */
    public function setCategorieDroit($categorieDroit)
    {
        $this->categorieDroit = $categorieDroit;

        return $this;
    }

    /**
     * Get categorieDroit
     *
     * @return string
     */
    public function getCategorieDroit()
    {
        return $this->categorieDroit;
    }

    /**
     * @return mixed
     */
    public function getTechnique()
    {
        return $this->technique;
    }

    /**
     * @param mixed $technique
     */
    public function setTechnique($technique)
    {
        $this->technique = $technique;
    }

}

