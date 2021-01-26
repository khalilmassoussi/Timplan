<?php

namespace TimSoft\GeneralBundle\Entity;


use CalendarBundle\Entity\Event;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Planning
 *
 * @ORM\Table(name="planning")
 * @ORM\Entity(repositoryClass="TimSoft\GeneralBundle\Repository\PlanningRepository")
 * @Gedmo\Loggable
 */
class Planning extends Event implements \JsonSerializable
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
     * @ORM\Column(type="datetime")
     */
    protected $start;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $end = null;
    /**
     * @var
     */
    protected $title;
    /**
     * @var
     * @ORM\Column(type="boolean", name="allDay")
     * @Gedmo\Versioned
     */
    protected $allDay;

    protected $eventColor;
    /**
     * @var
     * @ORM\ManyToOne(targetEntity="TimSoft\GeneralBundle\Entity\Utilisateur", inversedBy="plannings")
     */
    protected $utilisateur;
    /**
     * @var
     * @ORM\ManyToOne(targetEntity="TimSoft\CommandeBundle\Entity\LigneCommande", inversedBy="plannings")
     */
    protected $lc;
    /**
     * @var
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Versioned
     */
    protected $statut;
    /**
     * @var
     * @ORM\Column(type="boolean", length=255)
     */
    protected $jSupplementaire = false;
    protected $rap;
    /**
     * @var
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $facturation;
    /**
     * @var
     * @ORM\ManyToMany(targetEntity="TimSoft\GeneralBundle\Entity\Utilisateur")
     */
    protected $accompagnements;
    /**
     * @var
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $lieu;
    protected $eventTextColor;
    /**
     * @var
     * @ORM\ManyToOne(targetEntity="TimSoft\GeneralBundle\Entity\Utilisateur")
     */
    protected $confirmePar;
    /**
     * @var *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $commentaire;
    /**
     * @var
     * @ORM\OneToOne(targetEntity="TimSoft\GeneralBundle\Entity\FeuilleDePresence", mappedBy="intervention")
     */
    protected $feuille;
    /**
     * @var
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $alert;


    /**
     * Planning constructor.
     * @param $title
     * @param $start
     * @param null $end
     * @param null $lc
     */
    public function __construct($title, $start, $end, $lc = null)
    {
        $this->start = $start;
        $this->end = $end;
        $this->title = $title;
        $this->accompagnements = new ArrayCollection();
        $this->lc = $lc;
    }

    /**
     * @return mixed
     */
    public function getFeuille()
    {
        return $this->feuille;
    }

    /**
     * @param mixed $feuille
     */
    public function setFeuille($feuille)
    {
        $this->feuille = $feuille;
    }

    /**
     * Planning constructor.
     */


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
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @param mixed $start
     */


    /**
     * @return mixed
     */
    public function getEnd()
    {
        return $this->end;
    }

    public function setEnd($end)
    {
        $this->end = $end;
    }
    /**
     * @param mixed $end
     */

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */

    /**
     * @return mixed
     */
    public function getUtilisateur()
    {
        return $this->utilisateur;
    }

    /**
     * @param mixed $utilisateur
     */
    public function setUtilisateur($utilisateur)
    {
        $this->utilisateur = $utilisateur;
    }

    /**
     * @return mixed
     */
    public function getLc()
    {
        return $this->lc;
    }

    /**
     * @param mixed $lc
     */
    public function setLc($lc)
    {
        $this->lc = $lc;
    }

    /**
     * @return mixed
     */
    public function getStatut()
    {
        return $this->statut;
    }

    /**
     * @param mixed $statut
     */
    public function setStatut($statut)
    {
        $this->statut = $statut;
    }

    /**
     * @return mixed
     */
    public function getFacturation()
    {
        return $this->facturation;
    }

    /**
     * @param mixed $facturation
     */
    public function setFacturation($facturation)
    {
        $this->facturation = $facturation;
    }

    /**
     * @return mixed
     */
    public function getLieu()
    {
        return $this->lieu;
    }

    /**
     * @param mixed $lieu
     */
    public function setLieu($lieu)
    {
        $this->lieu = $lieu;
    }

    /**
     * @return mixed
     */
    public function isAllDay()
    {
        return $this->allDay;
    }

    /**
     * @param mixed $allDay
     */
    public function setAllDay($allDay): void
    {
        $this->allDay = $allDay;
    }

    /**
     * @param mixed $start
     */
    public function setStart($start): void
    {
        $this->start = $start;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     * @throws \ReflectionException
     */
    public function jsonSerialize()
    {
        $class = new \ReflectionClass($this->lc->getCommande());
        if ($this->statut == 'Confirmé') {
            $this->eventTextColor = '#FFFFFF';
            $this->eventColor = '#007bff';
        } elseif ($this->statut == 'En attente') {
            $this->eventColor = '#ff851b';
            $this->eventTextColor = '#FFFFFF';
        } elseif ($this->statut == 'Proposé') {
            $this->eventColor = '#ffc107';
            $this->eventTextColor = '#000000';
        } elseif ($this->statut == 'Terminé') {
            $this->eventColor = '#28a745';
            $this->eventTextColor = '#FFFFFF';
        } elseif ($this->statut == 'Rejeté') {
            $this->eventColor = '#dc3545';
            $this->eventTextColor = '#FFFFFF';
        }
        if ($this->end) {
            if ($this->accompagnements) {
                return array(
                    'id' => $this->id,
                    'title' => $this->lc->getCommande()->getClient()->getRaisonSociale(),
                    // 'title' => $this->title,
                    'allDay' => $this->allDay,
                    'start' => $this->start->format("Y-m-d H:i:s"),
                    'end' => $this->end->format("Y-m-d H:i:s"),
                    'Client' => array('raisonSociale' => $this->lc->getCommande()->getClient()->getRaisonSociale(),
                        'id' => $this->lc->getCommande()->getClient()->getId()
                    ),
                    'backgroundColor' => $this->eventColor,
                    'Statut' => $this->statut,
                    'Lieux' => $this->lc->getCommande()->getClient()->getAdresseClient(),
                    'Ville' => $this->lc->getCommande()->getClient()->getVilleClient(),
                    'Telephone' => $this->lc->getCommande()->getClient()->getTelephone(),
                    'JSupplementaire' => $this->jSupplementaire,
                    'Facturation' => $this->facturation,
                    'Intervenant' => $this->utilisateur->getPrenomUtilisateur() . ' ' . $this->utilisateur->getNomUtilisateur(),
                    'resourceIds' => [$this->utilisateur->getId(), $this->getAccompIds()],
                    'Accompagnements' => $this->accompagnements->toArray(),
//                    'IdAccompagnemant' => $this->accompagnement->getId(),
                    'textColor' => $this->eventTextColor,
                    'Lieu' => $this->lieu,
                    'UtilisateurClient' => $this->confirmePar,
                    'libelle' => $this->lc->getLibelle(),
                    'Commentaire' => $this->commentaire,
                    'description' => $this->lc->getLibelle(),
                    'nCommande' => $this->lc->getCommande()->getNCommande(),
                    'type' => $class->getShortName(),
                    'affaire' => $this->getLc()->getCommande()->getAffaire()
                );
            } else {
                return array(
                    'id' => $this->id,
                    'title' => $this->lc->getCommande()->getClient()->getRaisonSociale(),
                    // 'title' => $this->title,
                    'allDay' => $this->allDay,
                    'start' => $this->start->format("c"),
                    'end' => $this->end->format("c"),
                    'Client' => array('raisonSociale' => $this->lc->getCommande()->getClient()->getRaisonSociale(),
                        'id' => $this->lc->getCommande()->getClient()->getId()
                    ),
                    'backgroundColor' => $this->eventColor,
                    'Statut' => $this->statut,
                    'Lieux' => $this->lc->getCommande()->getClient()->getAdresseClient(),
                    'Ville' => $this->lc->getCommande()->getClient()->getVilleClient(),
                    'Telephone' => $this->lc->getCommande()->getClient()->getTelephone(),
                    'JSupplementaire' => $this->jSupplementaire,
//                    'Facturation' => $this->facturation,
                    'Intervenant' => $this->utilisateur->getPrenomUtilisateur() . ' ' . $this->utilisateur->getNomUtilisateur(),
                    'resourceId' => $this->utilisateur->getId(),
//                    'IdAccompagnemant' => $this->accompagnement->getId(),
                    'textColor' => $this->eventTextColor,
//                    'Lieu' => $this->lieu,
                    'UtilisateurClient' => $this->confirmePar,
                    'libelle' => $this->lc->getLibelle(),
                    'Commentaire' => $this->commentaire,
                    'description' => $this->lc->getLibelle(),
                    'type' => $class->getShortName()

                );
            }
        } else {
            return array(
                'id' => $this->id,
                'title' => $this->lc->getLibelle(),
                'allDay' => $this->allDay,
                'start' => $this->start->format("c"),
                'end' => $this->end,
                'Statut' => $this->statut
            );
        }
    }

    public function jRestantes()
    {
        if (!$this->allDay) {
            return 0.5;
        } else {
            return 1;
        }
    }

    /**
     * @return mixed
     */
    public function getJSupplementaire()
    {
        return $this->jSupplementaire;
    }

    /**
     * @param mixed $jSupplementaire
     */
    public function setJSupplementaire($jSupplementaire)
    {
        $this->jSupplementaire = $jSupplementaire;
    }

    /**
     * @return mixed
     */
    public function getAccompagnements()
    {
        return $this->accompagnements;
    }

    /**
     * @param mixed $accompagnement
     */
    public function setAccompagnements($accompagnement)
    {
        $this->accompagnements = $accompagnement;
    }

    public function addAccomp($user)
    {
        if (!$this->accompagnements->contains($user)) {
            $this->accompagnements->add($user);
        }
    }

    public function removeAccomp($user)
    {
        if ($this->accompagnements->contains($user)) {
            $this->accompagnements->removeElement($user);
        }
    }

    public function clearAll()
    {
        $this->accompagnements->clear();
    }

    /**
     * @return mixed
     */
    public function getConfirmePar()
    {
        return $this->confirmePar;
    }

    /**
     * @param mixed $confirmePar
     */
    public function setConfirmePar($confirmePar)
    {
        $this->confirmePar = $confirmePar;
    }

    /**
     * @return mixed
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }

    /**
     * @param mixed $commentaire
     */
    public function setCommentaire($commentaire)
    {
        $this->commentaire = $commentaire;
    }

    public function getAccompIds()
    {
        $ids = [];
        foreach ($this->accompagnements as $accompagnement) {
            $ids[] = $accompagnement->getId();
        }
        return $ids;
    }

    /**
     * @return mixed
     */
    public function getAlert()
    {
        return $this->alert;
    }

    /**
     * @param mixed $alert
     */
    public function setAlert($alert)
    {
        $this->alert = $alert;
    }


}

