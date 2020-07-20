<?php

namespace TimSoft\GeneralBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Dashboard
 *
 * @ORM\Table(name="dashboard")
 * @ORM\Entity(repositoryClass="TimSoft\GeneralBundle\Repository\DashboardRepository")
 */
class Dashboard
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
     * @var
     * @ORM\Column(type="string")
     */
    private $iframe;

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
    public function getIframe()
    {
        return $this->iframe;
    }

    /**
     * @param mixed $iframe
     */
    public function setIframe($iframe)
    {
        $this->iframe = $iframe;
    }

}
