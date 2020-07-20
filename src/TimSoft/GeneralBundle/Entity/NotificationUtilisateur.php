<?php

namespace TimSoft\GeneralBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NotificationUtilisateur
 *
 * @ORM\Table(name="notification_utilisateur")
 * @ORM\Entity(repositoryClass="TimSoft\GeneralBundle\Repository\NotificationUtilisateurRepository")
 */
class NotificationUtilisateur
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
     * Plusieurs notificationUser sont relatifs à une notification
     * @ORM\ManyToOne(targetEntity="TimSoft\NotificationBundle\Entity\Notification", inversedBy="NotificationUtilisateur")
     * @ORM\JoinColumn(name="Notification", referencedColumnName="id", onDelete="CASCADE")
     */
    private $Notification;
    
     /**
     * Plusieurs notificationUser sont relatifs à un utilisateur
     * @ORM\ManyToOne(targetEntity="TimSoft\GeneralBundle\Entity\Utilisateur", inversedBy="NotificationUtilisateur")
     * @ORM\JoinColumn(name="Utilisateur", referencedColumnName="id", onDelete="CASCADE")
     */
    private $Utilisateur;
    

    /**
     * @var bool
     *
     * @ORM\Column(name="vu", type="boolean")
     */
    private $vu;




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
     * Set vu
     *
     * @param boolean $vu
     *
     * @return NotificationUtilisateur
     */
    public function setVu($vu)
    {
        $this->vu = $vu;

        return $this;
    }

    /**
     * Get vu
     *
     * @return boolean
     */
    public function getVu()
    {
        return $this->vu;
    }

    /**
     * Set notification
     *
     * @param \TimSoft\NotificationBundle\Entity\Notification $notification
     *
     * @return NotificationUtilisateur
     */
    public function setNotification(\TimSoft\NotificationBundle\Entity\Notification $notification = null)
    {
        $this->Notification = $notification;

        return $this;
    }

    /**
     * Get notification
     *
     * @return \TimSoft\NotificationBundle\Entity\Notification
     */
    public function getNotification()
    {
        return $this->Notification;
    }

    /**
     * Set utilisateur
     *
     * @param \TimSoft\GeneralBundle\Entity\Utilisateur $utilisateur
     *
     * @return NotificationUtilisateur
     */
    public function setUtilisateur(\TimSoft\GeneralBundle\Entity\Utilisateur $utilisateur = null)
    {
        $this->Utilisateur = $utilisateur;

        return $this;
    }

    /**
     * Get utilisateur
     *
     * @return \TimSoft\GeneralBundle\Entity\Utilisateur
     */
    public function getUtilisateur()
    {
        return $this->Utilisateur;
    }
}
