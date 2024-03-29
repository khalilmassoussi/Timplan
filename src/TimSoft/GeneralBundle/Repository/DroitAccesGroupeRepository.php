<?php

namespace TimSoft\GeneralBundle\Repository;

/**
 * DroitAccesGroupeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DroitAccesGroupeRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param $Route
     * @param $Role
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
     public function getAutorisationGroupe($Route,$Role)
    {
        $ID = $this->getEntityManager()->getRepository('TimSoftGeneralBundle:DroitAcces')->getID($Route);

        return $this->createQueryBuilder('g')
                        ->select('g')
                        ->where('g.droit =  :ID')
                        ->andWhere('g.role = :Role')
                        ->setParameter('ID', $ID)
                        ->setParameter('Role', $Role)
                        ->getQuery()
                        ->getOneOrNullResult();
    }

     public function getGroupeAutorises($ID)
    {
        return $this->createQueryBuilder('g')
                        ->select('g.role')
                        ->where('g.droit =  :ID')
                        ->setParameter('ID', $ID)
                        ->getQuery()
                        ->getResult();
    }

     public function autoriserGroupe($ID,$Groupe)
    {

        return intval($this->createQueryBuilder('g')
                        ->select('COUNT(g)')
                        ->where('g.droit = :ID')
                        ->andWhere('g.role = :Groupe')
                        ->setParameter('ID', $ID)
                        ->setParameter('Groupe', $Groupe)
                        ->getQuery()
                        ->getSingleScalarResult());
    }
}
