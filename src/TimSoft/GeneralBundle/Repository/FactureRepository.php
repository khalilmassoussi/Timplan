<?php

namespace TimSoft\GeneralBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;

/**
 * FactureRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FactureRepository extends \Doctrine\ORM\EntityRepository
{
    // facture en general
    public function getFactureParType($typeFacture) {
        
        return $this->createQueryBuilder('l')
                        ->select('l')
                        ->where('l.statutFacture =  :statut')
                        ->setParameter('statut', $typeFacture)
                        ->getQuery()
                        ->getResult();
}

public function getMontantParMois($Mois,$Annee) {
        
    $emConfig = $this->getEntityManager()->getConfiguration();
        $emConfig->addCustomDatetimeFunction('MONTH', 'DoctrineExtensions\Query\Mysql\Month');
        $emConfig->addCustomDatetimeFunction('YEAR', 'DoctrineExtensions\Query\Mysql\Year');
        return intval($this->createQueryBuilder('l')
                        ->select('sum(l.montantTTCFacture)')
                        ->where('MONTH(l.dateFacture) = :date ')
                        ->andWhere('YEAR(l.dateFacture) = :Annee')
                        ->setParameter('date', $Mois )
                        ->setParameter('Annee', $Annee)
                        ->getQuery()
                        ->getSingleScalarResult());
}


     public function getNBFacture() {
 
        return intval($this->createQueryBuilder('l')
                        ->select('COUNT(l)')
                        ->getQuery()
                        ->getSingleScalarResult());
    }
    
    public function getFactureEnCours() {
        
        return $this->createQueryBuilder('l')
                        ->select('l')
                        ->where('l.statutFacture = :statut1 OR l.statutFacture = :statut2 OR l.statutFacture = :statut3')
                        ->setParameters(new ArrayCollection(array (new Parameter  ('statut1', 'Non traitée' ) , new Parameter ('statut2', 'Validée' ) , new Parameter ('statut3', 'Règlement en cours' ))))
                        ->getQuery()
                        ->getResult();
}
    
    public function getNBFactureEnCours() {
        
        return intval($this->createQueryBuilder('l')
                        ->select('COUNT(l)')
                        ->where('l.statutFacture = :statut1 OR l.statutFacture = :statut2 OR l.statutFacture = :statut3')
                        ->setParameters(new ArrayCollection(array (new Parameter  ('statut1', 'Non traitée' ) , new Parameter ('statut2', 'Validée' ) , new Parameter ('statut3', 'Règlement en cours' ))))
                        ->getQuery()
                        ->getSingleScalarResult());
}

public function getMontantFactureEnCours() {
        
        return $this->createQueryBuilder('l')
                        ->select('sum(l.montantTTCFacture)')
                        ->where('l.statutFacture = :statut1 OR l.statutFacture = :statut2 OR l.statutFacture = :statut3')
                        ->setParameters(new ArrayCollection(array (new Parameter  ('statut1', 'Non traitée' ) , new Parameter ('statut2', 'Validée' ) , new Parameter ('statut3', 'Règlement en cours' ))))
                        ->getQuery()
                ->getSingleScalarResult();
}

 public function getNBFactureParStatut($statut) {
        
        return intval($this->createQueryBuilder('l')
                        ->select('COUNT(l)')
                        ->where('l.statutFacture = :statut ')
                        ->setParameter('statut', $statut)
                        ->getQuery()
                        ->getSingleScalarResult());
}

// Facture % client
 public function getNBFactureClient($Client) {
 
        return intval($this->createQueryBuilder('l')
                        ->select('COUNT(l)')
                        ->where('l.client = :Client ')
                        ->setParameter('Client', $Client)
                        ->getQuery()
                        ->getSingleScalarResult());
    }
    
// Facture en cours (Non traitée,Règlement en cours ou validée  )  par cleint
     public function getFactureEnCoursCleint($Client) {
        
        return $this->createQueryBuilder('f')
                        ->select('f')
                        ->where('f.statutFacture = :statut1 OR f.statutFacture = :statut2 OR f.statutFacture = :statut3')
                        ->andWhere('f.client = :Client')
                        ->setParameters(new ArrayCollection(array (new Parameter  ('statut1', 'Non traitée' ) , new Parameter ('statut2', 'Validée' ) , new Parameter ('statut3', 'Règlement en cours' ))))
                        ->setParameter('Client', $Client)
                        ->getQuery()
                        ->getResult();
}


//Montant des factures en cours par client
public function getMontantFactureEnCoursParClient($Client) {
        
        return $this->createQueryBuilder('f')
                        ->select('sum(f.montantTTCFacture)')
                        ->where('f.statutFacture = :statut1 OR f.statutFacture = :statut2 OR f.statutFacture = :statut3')
                        ->andWhere('f.client = :Client')
                        ->setParameters(new ArrayCollection(array (new Parameter  ('statut1', 'Non traitée' ) , new Parameter ('statut2', 'Validée' ) , new Parameter ('statut3', 'Règlement en cours' ))))
                        ->setParameter('Client', $Client)
                        ->getQuery()
                        ->getSingleScalarResult();
}

//public function getFactureParTypeParClient($typeFacture, $Client) {
//        
//        return $this->createQueryBuilder('f')
//                        ->select('f')
//                        ->where('f.statutFacture =  :statut')
//                        ->andWhere('f.client = :Client')
//                        ->setParameter('statut', $typeFacture)
//                        ->setParameter('Client', $Client)
//                        ->getQuery()
//                        ->getResult();
//}

 public function getFactureParStatutParClient($statut,$Client) {
        
        return $this->createQueryBuilder('l')
                        ->select('l')
                        ->where('l.statutFacture = :statut ')
                        ->andWhere('l.client = :Client ')
                        ->setParameter('statut', $statut)
                        ->setParameter('Client', $Client)
                        ->getQuery()
                        ->getResult();
}
}