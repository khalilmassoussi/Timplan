<?php

namespace TimSoft\TasksBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Activite
 *
 * @ORM\Table(name="activite")
 * @ORM\Entity(repositoryClass="TimSoft\TasksBundle\Repository\ActiviteRepository")
 */
class Activite implements \JsonSerializable
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
     * @var
     * @ORM\OneToMany(targetEntity="TimSoft\TasksBundle\Entity\Task", mappedBy="activite", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $tasks;

    /**
     * Activite constructor.
     * @param $tasks
     */
    public function __construct()
    {
        $this->tasks = new ArrayCollection();
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
     * Set libelle.
     *
     * @param string $libelle
     *
     * @return Activite
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get libelle.
     *
     * @return string
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * @return mixed
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    /**
     * @param mixed $taks
     */
    public function setTasks($tasks): void
    {
        $this->tasks = $tasks;
    }

    public function __toString()
    {
        return $this->libelle;
    }

    public function addTask(Task $task)
    {
        $task->setActivite($this);

        $this->tasks->add($task);
    }

    public function removeTask(Task $task)
    {
        $this->tasks->removeElement($task);
        $task->setActivite(null);
    }

    public function contains(Task $task)
    {
        return $this->tasks->contains($task);
    }

    public function jsonSerialize()
    {
        return array(
            'id' => $this->id,
            'libelle' => $this->libelle,
            'tasks' => $this->tasks->toArray()
        );
    }
}
