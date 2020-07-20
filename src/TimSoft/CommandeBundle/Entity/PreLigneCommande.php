<?php


namespace TimSoft\CommandeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class PreLigneCommande
 * @ORM\Entity(repositoryClass="TimSoft\CommandeBundle\Repository\PreLigneCommandeRepository")
 * @package TimSoft\CommandeBundle\Entity
 */
class PreLigneCommande extends LigneCommande
{

}