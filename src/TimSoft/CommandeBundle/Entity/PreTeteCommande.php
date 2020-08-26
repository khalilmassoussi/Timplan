<?php


namespace TimSoft\CommandeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class PreTeteCommande
 * @package TimSoft\CommandeBundle\Entity
 * @ORM\Entity(repositoryClass="TimSoft\CommandeBundle\Repository\PreTeteCommandeRepository")
 *
 */
class PreTeteCommande extends TeteCommande implements \JsonSerializable
{
    /**
     * @var
     * @ORM\Column(type="boolean")
     */
    private $archive;
    /**
     * @var
     * @ORM\OneToOne(targetEntity="TimSoft\CommandeBundle\Entity\TeteCommande")
     */
    private $commande;

    /**
     * @return mixed
     */
    public function getArchive()
    {
        return $this->archive;
    }

    /**
     * @param mixed $archive
     */
    public function setArchive($archive)
    {
        $this->archive = $archive;
    }

    /**
     * @return mixed
     */
    public function getCommande()
    {
        return $this->commande;
    }

    /**
     * @param mixed $commande
     */
    public function setCommande($commande): void
    {
        $this->commande = $commande;
    }

    public function jsonSerialize()
    {
        $return = array(
            'id' => $this->id,
            'nCommande' => $this->nCommande,
            'Client' => $this->client->getRaisonSociale(),
            'Date' => $this->datePiece->format('c'),
            'RAP' => $this->totalRAP(),
            'Bus' => $this->Bus(),
        );
        if ($this->buManager) {
            $return['BusinessManager'] = $this->buManager->__toString();
        } else {
            $return['BusinessManager'] = null;

        }
        if ($this->commande) {
            $return['commande'] = $this->commande->getNCommande();
        } else {
            $return['commande'] = null;

        }
        return $return;
    }
}
