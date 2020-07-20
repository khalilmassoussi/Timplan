<?php


namespace TimSoft\CommandeBundle\Repository;


class PreLigneCommandeRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param $id
     * @param $nligne
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findCmd($id, $nligne)
    {
        $query = $this->createQueryBuilder('c')
            ->select('c')
            ->where('c.commande = :id')
            ->andWhere('c.nLigne = :nligne')
            ->setParameter('id', $id)
            ->setParameter('nligne', $nligne)
            ->getQuery();
        return $query->getOneOrNullResult();
    }
}