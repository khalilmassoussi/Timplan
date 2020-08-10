<?php

namespace TimSoft\TasksBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TaskEvent
 *
 * @ORM\Table(name="task_event")
 * @ORM\Entity(repositoryClass="TimSoft\TasksBundle\Repository\TaskEventRepository")
 */
class TaskEvent implements \JsonSerializable
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
     * @var
     * @ORM\Column(type="string", columnDefinition="ENUM('Télétravail', 'Bureau', 'Extérieur')", nullable=false)
     */
    protected $site;
    /**
     * @var
     * @ORM\Column(type="string", columnDefinition="ENUM('Important', 'Urgent', 'Moyen', 'Peu')", nullable=false)
     */
    protected $etiquette;
    /**
     * @var
     * @ORM\Column(type="string", columnDefinition="ENUM('To DO', 'In Progress', 'Done', 'Bloqué')", nullable=false)
     */

    protected $statut;
    /**
     * @var
     * @ORM\Column(type="string", nullable=true)
     */
    protected $motif;
    /**
     * @var
     * @ORM\Column(type="integer")
     */
    protected $progression;

    /**
     * @var
     * @ORM\ManyToOne(targetEntity="TimSoft\TasksBundle\Entity\Task")
     */
    protected $task;
    /**
     * @var
     * @ORM\Column(type="datetime")
     */
    protected $start;
    /**
     * @var
     * @ORM\Column(type="datetime")
     */
    protected $end;
    /**
     * @var
     * @ORM\ManyToOne(targetEntity="TimSoft\GeneralBundle\Entity\Utilisateur")
     */
    protected $utilisateur;
    /**
     * @var
     * @ORM\ManyToOne(targetEntity="TimSoft\GeneralBundle\Entity\Client")
     */
    protected $client;
    /**
     * @var
     * @ORM\Column(type="string")
     */
    protected $rapport;
    /**
     * @var
     * @ORM\Column(type="boolean")
     */
    protected $allDay;
    protected $eventTextColor;
    protected $eventColor;
    /**
     * @var
     * @ORM\ManyToOne(targetEntity="TimSoft\TasksBundle\Entity\Activite")
     */
    protected $activite;

    /**
     * TaskEvent constructor.
     */
    public function __construct()
    {

    }


    /**
     * Get id.
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
    public function getTask()
    {
        return $this->task;
    }

    /**
     * @param mixed $task
     */
    public function setTask($task): void
    {
        $this->task = $task;
    }

    /**
     * @return mixed
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param mixed $site
     */
    public function setSite($site): void
    {
        $this->site = $site;
    }

    /**
     * @return mixed
     */
    public function getEtiquette()
    {
        return $this->etiquette;
    }

    /**
     * @param mixed $etiquette
     */
    public function setEtiquette($etiquette): void
    {
        $this->etiquette = $etiquette;
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
    public function setStatut($statut): void
    {
        $this->statut = $statut;
    }

    /**
     * @return mixed
     */
    public function getMotif()
    {
        return $this->motif;
    }

    /**
     * @param mixed $motif
     */
    public function setMotif($motif): void
    {
        $this->motif = $motif;
    }

    /**
     * @return mixed
     */
    public function getProgression()
    {
        return $this->progression;
    }

    /**
     * @param mixed $progression
     */
    public function setProgression($progression): void
    {
        $this->progression = $progression;
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
    public function setStart($start): void
    {
        $this->start = $start;
    }


    /**
     * @return mixed
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @param mixed $end
     */
    public function setEnd($end): void
    {
        $this->end = $end;
    }

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
    public function setUtilisateur($utilisateur): void
    {
        $this->utilisateur = $utilisateur;
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
    public function setClient($client): void
    {
        $this->client = $client;
    }

    /**
     * @return mixed
     */
    public function getRapport()
    {
        return $this->rapport;
    }

    /**
     * @param mixed $rapport
     */
    public function setRapport($rapport): void
    {
        $this->rapport = $rapport;
    }

    /**
     * @return mixed
     */
    public function getActivite()
    {
        return $this->activite;
    }

    /**
     * @param mixed $activite
     */
    public function setActivite($activite): void
    {
        $this->activite = $activite;
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

    public function jsonSerialize()
    {
        $class = new \ReflectionClass($this);
//        if ($this->statut == 'To DO') {
//            $this->eventTextColor = '#FFFFFF';
//        } elseif ($this->statut == 'En attente') {
//            $this->eventColor = '#FFA500';
//            $this->eventTextColor = '#FFFFFF';
//        } elseif ($this->statut == 'In Progress') {
//            $this->eventColor = '#FFF380';
//            $this->eventTextColor = '#000000';
//        } elseif ($this->statut == 'Done') {
//            $this->eventColor = '#008000';
//            $this->eventTextColor = '#FFFFFF';
//        } elseif ($this->statut == 'Bloqué') {
//            $this->eventColor = '#FF0000';
//            $this->eventTextColor = '#FFFFFF';
//        }
        $this->eventColor = '#81B5D4';
        $this->eventTextColor = '#FFFFFF';
        return array(
            'id' => $this->id,

            'title' => $this->client->getRaisonSociale(),
            // 'title' => $this->title,
            'allDay' => $this->allDay,
            'start' => $this->start->format("Y-m-d H:i:s"),
            'end' => $this->end->format("Y-m-d H:i:s"),
            'Client' => array('raisonSociale' => $this->client->getRaisonSociale(),
                'id' => $this->client->getId()
            ),
            'backgroundColor' => $this->eventColor,
            'Statut' => $this->statut,
            'Intervenant' => $this->utilisateur->getPrenomUtilisateur() . ' ' . $this->utilisateur->getNomUtilisateur(),
            'idIntervenant' => $this->utilisateur->getId(),
            'textColor' => $this->eventTextColor,
            'Lieu' => $this->site,
            'resourceIds' => [$this->utilisateur->getId()],
            'rapport' => $this->rapport,
            'type' => $class->getShortName(),
            'activite' => $this->activite,
            'task' => $this->task,
            'libelle' => $this->task,
            'etiquette' => $this->etiquette
        );
    }
}
