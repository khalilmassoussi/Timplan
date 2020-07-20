<?php

namespace TimSoft\BuBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use TimSoft\GeneralBundle\Entity\Utilisateur;

/**
 * Bu
 *
 * @ORM\Table(name="bu")
 * @ORM\Entity(repositoryClass="TimSoft\BuBundle\Repository\BuRepository")
 */
class Bu implements \JsonSerializable
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
     * @ORM\Column(name="libelle", type="string", length=255)
     */
    private $libelle;

    /**
     * @var array
     *
     * @ORM\ManyToMany(targetEntity="TimSoft\GeneralBundle\Entity\Utilisateur", mappedBy="bus")
     */
    private $utilisateurs;

    /**
     * Bu constructor.
     */
    public function __construct()
    {
        $this->utilisateurs = new ArrayCollection();
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
     * Set libelle
     *
     * @param string $libelle
     *
     * @return Bu
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get libelle
     *
     * @return string
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * Set utilisateurs
     *
     * @param array $utilisateurs
     *
     * @return Bu
     */
    public function setUtilisateurs($utilisateurs)
    {
        $this->utilisateurs = $utilisateurs;

        return $this;
    }

    /**
     * Get utilisateurs
     *
     * @return array
     */
    public function getUtilisateurs()
    {
        return $this->utilisateurs;
    }

    public function add($user)
    {
        $this->utilisateurs->add($user);
    }

    public function remove(Utilisateur $user)
    {
        $this->utilisateurs->removeElement($user);
    }

    public function getChef()
    {
        foreach ($this->utilisateurs as $utilisateur) {
            if ($utilisateur->getRoleUtilisateur() === 'ROLE_CHEF') {
                return $utilisateur;
            }
        }
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
        // TODO: Implement jsonSerialize() method.
        return get_object_vars($this);
    }
}

