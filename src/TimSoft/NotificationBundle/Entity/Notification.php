<?php

namespace TimSoft\NotificationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use TimSoft\GeneralBundle\Entity\Utilisateur;
use SBC\NotificationsBundle\Model\BaseNotification;

/**
 * Notification
 *
 * @ORM\Table(name="notification")
 * @ORM\Entity(repositoryClass="TimSoft\NotificationBundle\Repository\NotificationRepository")
 */
class Notification  extends BaseNotification implements \JsonSerializable
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
     * @ORM\ManyToOne(targetEntity="TimSoft\GeneralBundle\Entity\Utilisateur")
     * @ORM\JoinColumn(name="Responsable", referencedColumnName="id" , onDelete="CASCADE")
     */
    private $responsable;

     /**
     * Une notification a plusieurs NotificationUtilisateur.
     * @ORM\OneToMany(targetEntity="TimSoft\GeneralBundle\Entity\NotificationUtilisateur", mappedBy="Notification" , cascade={"persist"})
     */
    private $NotificationUtilisateur;

    
    function jsonSerialize()
    {
        return get_object_vars($this);
    }

    /**
     * Get seen
     *
     * @return boolean
     */
    public function getSeen()
    {
        return $this->seen;
    }

    /**
     * Set responsable
     *
     * @param \TimSoft\GeneralBundle\Entity\Utilisateur $responsable
     *
     * @return Notification
     */
    public function setResponsable(\TimSoft\GeneralBundle\Entity\Utilisateur $responsable = null)
    {
        $this->responsable = $responsable;

        return $this;
    }

    /**
     * Get responsable
     *
     * @return \TimSoft\GeneralBundle\Entity\Utilisateur
     */
    public function getResponsable()
    {
        return $this->responsable;
    }

    /**
     * Add notificationUtilisateur
     *
     * @param \TimSoft\GeneralBundle\Entity\NotificationUtilisateur $notificationUtilisateur
     *
     * @return Notification
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
}
