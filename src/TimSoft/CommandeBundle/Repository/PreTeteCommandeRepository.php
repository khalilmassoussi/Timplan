<?php

namespace TimSoft\CommandeBundle\Repository;

/**
 * TeteCommandeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */

/**
 * Class TeteCommandeRepository
 * @package TimSoft\CommandeBundle\Repository
 */
class PreTeteCommandeRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param $number
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getByNumber($number)
    {
        return $this->createQueryBuilder('c')
            ->select('c')
            ->where('c.nCommande = :number')
            ->setParameter('number', $number)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param $Mois
     * @param $Annee
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function chiffreBu()
    {
        $emConfig = $this->getEntityManager()->getConfiguration();
        $emConfig->addCustomDatetimeFunction('MONTH', 'DoctrineExtensions\Query\Mysql\Month');
        $emConfig->addCustomDatetimeFunction('YEAR', 'DoctrineExtensions\Query\Mysql\Year');
        return intval($this->createQueryBuilder('l')
            ->select('sum(l.prixTotal)')
            ->groupBy('l.lignCommandes.bu')
            ->getQuery()
            ->getSingleScalarResult());
    }

    public function getUniqueClients()
    {
        return $this->createQueryBuilder('c')
            ->select('client.raisonSociale')
            ->leftJoin('c.client', 'client')
            ->distinct("c.client")
            ->getQuery()
            ->getResult();
    }

    public function getUniqueDate()
    {
        return $this->createQueryBuilder('c')
            ->select('c.datePiece')
            ->distinct("c.datePiece")
            ->getQuery()
            ->getResult();
    }

    public function findAll()
    {
        return $this->createQueryBuilder('c')
            ->where('c INSTANCE OF TimSoft\CommandeBundle\Entity\PreTeteCommande')
            ->getQuery()
            ->getResult();
    }
//    public function getUniqueBU($id)
//    {
//        return $this->createQueryBuilder('c')
//            ->select('c.')
//            ->where('c.id = :id')
//            ->setParameter('id', $id)
//            ->distinct()
//            ->getQuery()
//            ->getResult();
//    }
}