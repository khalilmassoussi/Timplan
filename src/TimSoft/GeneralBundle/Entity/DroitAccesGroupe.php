<?php

namespace TimSoft\GeneralBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DroitAccesGroupe
 *
 * @ORM\Table(name="droit_acces_groupe")
 * @ORM\Entity(repositoryClass="TimSoft\GeneralBundle\Repository\DroitAccesGroupeRepository")
 */
class DroitAccesGroupe implements \JsonSerializable
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
     * @ORM\JoinColumn(name="Droit", referencedColumnName="id", onDelete="CASCADE")
     */
    private $droit;

    /**
     * @var string
     *
     * @ORM\Column(name="Role", type="string", length=30)
     */
    private $role;

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
     * Set role
     *
     * @param string $role
     *
     * @return DroitAccesGroupe
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set droit
     *
     * @param \TimSoft\GeneralBundle\Entity\DroitAcces $droit
     *
     * @return DroitAccesGroupe
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
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return array(
            'route' => $this->droit->getRouteDroit(),
            'role' => $this->role
        );
    }
}
