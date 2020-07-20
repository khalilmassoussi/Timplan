<?php

namespace TimSoft\GeneralBundle\Repository;

use DoctrineExtensions\Query\Mysql;
/**
 * RapportInterventionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class RapportInterventionRepository extends \Doctrine\ORM\EntityRepository
{
    
    public function getFeuilleSansRapport() {
 
        return createQuery('SELECT f FROM TimSoftGeneralBundle:FeuilleDePresence f  WHERE f.id  NOT IN ( select r.FeuilleDePresence FROM TimSoftGeneralBundle:Rapport r)');
}
// rapport en general
public function getNBRapport() {
        
        return intval($this->createQueryBuilder('r')
                        ->select('count(r)')
                        ->getQuery()
                        ->getSingleScalarResult());
}
public function getRapportParType($typeIntervention) {
        
        return $this->createQueryBuilder('r')
                        ->select('r')
                        ->where('r.typeIntervention =  :statut')
                        ->setParameter('statut', $typeIntervention)
                        ->getQuery()
                        ->getResult();
}
    public function getNBRapportParType($typeIntervention) {
 
        return intval($this->createQueryBuilder('r')
                        ->select('COUNT(r)')
                        ->where('r.typeIntervention =  :type')
                        ->setParameter('type', $typeIntervention)
                        ->getQuery()
                        ->getSingleScalarResult());
    }
    
    public function getNBRapportParMois($Mois,$Annee) {
        $emConfig = $this->getEntityManager()->getConfiguration();
        $emConfig->addCustomDatetimeFunction('MONTH', 'DoctrineExtensions\Query\Mysql\Month');
        $emConfig->addCustomDatetimeFunction('YEAR', 'DoctrineExtensions\Query\Mysql\Year');

 
        return intval($this->createQueryBuilder('r')
                        ->select('COUNT(r)')
                        ->from('TimSoftGeneralBundle:FeuilleDePresence' ,'f')
                        ->where('r.FeuilleDePresence = f.id')
                        ->andWhere('MONTH(f.dateIntervention) = :Mois')
                        ->andWhere('YEAR(f.dateIntervention) = :Annee')
                        ->setParameter('Mois', $Mois)
                        ->setParameter('Annee', $Annee)
                        ->getQuery()
                        ->getSingleScalarResult());
        
       
        
    }
    
    public function getNBRapportParTypeMois($Validation, $Mois,$Annee) {
        $emConfig = $this->getEntityManager()->getConfiguration();
        $emConfig->addCustomDatetimeFunction('MONTH', 'DoctrineExtensions\Query\Mysql\Month');
        $emConfig->addCustomDatetimeFunction('YEAR', 'DoctrineExtensions\Query\Mysql\Year');
 
        return  intval($this->createQueryBuilder('r')
                        ->select('COUNT(r)')
                        ->from('TimSoftGeneralBundle:FeuilleDePresence' ,'f')
                        ->where('r.FeuilleDePresence = f.id')
                        ->andWhere('r.typeIntervention =  :validation')
                        ->andWhere('MONTH(f.dateIntervention) = :Mois')
                        ->andWhere('YEAR(f.dateIntervention) = :Annee')
                        ->setParameter('validation', $Validation)
                        ->setParameter('Mois', $Mois)
                        ->setParameter('Annee', $Annee)
                        ->getQuery()
                        ->getSingleScalarResult());
        //bech nchouf el requete
        //die(print_r($qb->getQuery()->getSQL()));
        //return $qb->getQuery()
                        
        
       
        
    }
    public function getRapportParValidation($validation) {
 
        return $this->createQueryBuilder('r')
                        ->select('r')
                        ->where('r.confirmationDeInterventionParClient =  :validation')
                        ->setParameter('validation', $validation)
                        ->getQuery()
                        ->getResult();
    }
    
     public function getNBRapportParValidation($validation) {
 
        return intval($this->createQueryBuilder('r')
                        ->select('COUNT(r)')
                        ->where('r.confirmationDeInterventionParClient =  :validation')
                        ->setParameter('validation', $validation)
                        ->getQuery()
                        ->getSingleScalarResult());
    }
    
    
    
    
    //Rapport selon Client
//    public function getNBRapportClient($Client) {
//        
//        return intval($this->createQueryBuilder('r')
//                        ->select('count(r)')
//                        ->from('TimSoftGeneralBundle:FeuilleDePresence' ,'f')
//                        ->where('f.client = :Client')
//                        ->setParameter('Client', $Client)
//                        ->getQuery()
//                        ->getSingleScalarResult());
//}

 public function getRapportClient($Client) {
        
        return $this->createQueryBuilder('r')
                        ->select('r')
                        ->from('TimSoftGeneralBundle:FeuilleDePresence' ,'f')
                        ->where('r.FeuilleDePresence = f.id')
                        ->andWhere('f.client = :Client')
                        ->setParameter('Client', $Client)
                        ->getQuery()
                        ->getResult();
}
//public function getRapportParTypeParClient($typeIntervention,$Client) {
//        
//        return   $this->createQueryBuilder('count(r)')
//                        ->select('r')
//                        ->from('TimSoftGeneralBundle:FeuilleDePresence' ,'f')
//                        ->where('f.client = :Client')
//                        ->andWhere('r.typeIntervention =  :typeIntervention')
//                        ->setParameter('Client', $Client)
//                        ->setParameter('typeIntervention', $typeIntervention)
//                        ->getQuery()
//                        ->getResult();
//}

public function getNBRapportParTypeParClient($typeIntervention,$Client) {
        
        return  intval($this->createQueryBuilder('r')
                        ->select('COUNT(r)')
                        ->from('TimSoftGeneralBundle:FeuilleDePresence' ,'f')
                        ->where('f.client = :Client')
                        ->andWhere('r.typeIntervention =  :typeIntervention')
                        ->setParameter('Client', $Client)
                        ->setParameter('typeIntervention', $typeIntervention)
                        ->getQuery()
                        ->getSingleScalarResult());
}

    public function getNBRapportParMoisParClient($Mois,$Annee,$Client) {
        $emConfig = $this->getEntityManager()->getConfiguration();
        $emConfig->addCustomDatetimeFunction('MONTH', 'DoctrineExtensions\Query\Mysql\Month');
        $emConfig->addCustomDatetimeFunction('YEAR', 'DoctrineExtensions\Query\Mysql\Year');

 
        return intval($this->createQueryBuilder('r')
                        ->select('COUNT(r)')
                        ->from('TimSoftGeneralBundle:FeuilleDePresence' ,'f')
                        ->where('r.FeuilleDePresence = f.id')
                        ->andWhere('f.client = :Client')
                        ->setParameter('Client', $Client)
                        ->andWhere('MONTH(f.dateIntervention) = :Mois')
                        ->andWhere('YEAR(f.dateIntervention) = :Annee')
                        ->setParameter('Mois', $Mois)
                        ->setParameter('Annee', $Annee)
                        ->getQuery()
                        ->getSingleScalarResult());
    }
//    
    public function getNBRapportParTypeMoisParClient($Validation, $Mois,$Annee,$Client) {
        $emConfig = $this->getEntityManager()->getConfiguration();
        $emConfig->addCustomDatetimeFunction('MONTH', 'DoctrineExtensions\Query\Mysql\Month');
        $emConfig->addCustomDatetimeFunction('YEAR', 'DoctrineExtensions\Query\Mysql\Year');
 
        return  intval($this->createQueryBuilder('r')
                        ->select('COUNT(r)')
                        ->from('TimSoftGeneralBundle:FeuilleDePresence' ,'f')
                        ->where('r.FeuilleDePresence = f.id')
                        ->andWhere('f.client = :Client')
                        ->andWhere('r.typeIntervention =  :validation')
                        ->andWhere('MONTH(f.dateIntervention) = :Mois')
                        ->andWhere('YEAR(f.dateIntervention) = :Annee')
                        ->setParameter('Client', $Client)
                        ->setParameter('validation', $Validation)
                        ->setParameter('Mois', $Mois)
                        ->setParameter('Annee', $Annee)
                        ->getQuery()
                        ->getSingleScalarResult());
    }
    public function getRapportParValidationParClient($validation, $Client) {
 
        return $this->createQueryBuilder('r')
                        ->select('r')
                        ->from('TimSoftGeneralBundle:FeuilleDePresence' ,'f')
                        ->where('r.FeuilleDePresence = f.id')
                        ->andWhere('f.client = :Client')
                        ->andWhere('r.confirmationDeInterventionParClient =  :validation')
                        ->setParameter('Client', $Client)
                        ->setParameter('validation', $validation)
                        ->getQuery()
                        ->getResult();
    }
    
     public function getRapportConsultant($Consultant) {
        
        return $this->createQueryBuilder('r')
                        ->select('r')
                        ->from('TimSoftGeneralBundle:FeuilleDePresence' ,'f')
                        ->where('r.FeuilleDePresence = f.id')
                        ->andWhere('f.intervenant = :Intervenant')
                        ->setParameter('Intervenant', $Consultant)
                        ->getQuery()
                        ->getResult();
}

 public function getRapportParValidationParConsultant($Consultant,$Validation) {
        
        return $this->createQueryBuilder('r')
                        ->select('r')
                        ->from('TimSoftGeneralBundle:FeuilleDePresence' ,'f')
                        ->where('r.FeuilleDePresence = f.id')
                        ->andWhere('f.intervenant = :Intervenant')
                        ->andWhere('r.confirmationDeInterventionParClient =  :validation')
                        ->setParameter('Intervenant', $Consultant)
                        ->setParameter('validation', $Validation)
                        ->getQuery()
                        ->getResult();
}

 public function getNBRapportParTypeMoisParConsultant($Type, $Mois,$Annee,$Consultant) {
        $emConfig = $this->getEntityManager()->getConfiguration();
        $emConfig->addCustomDatetimeFunction('MONTH', 'DoctrineExtensions\Query\Mysql\Month');
        $emConfig->addCustomDatetimeFunction('YEAR', 'DoctrineExtensions\Query\Mysql\Year');
//        var_dump($Consultant);
        return  intval($this->createQueryBuilder('r')
                        ->select('COUNT(r)')
                        ->from('TimSoftGeneralBundle:FeuilleDePresence' ,'f')
                        ->where('r.FeuilleDePresence = f.id')
                        ->andWhere('f.intervenant = :Intervenant')
                        ->andWhere('r.typeIntervention =  :Type')
                        ->andWhere('MONTH(f.dateIntervention) = :Mois')
                        ->andWhere('YEAR(f.dateIntervention) = :Annee')
                        ->setParameter('Intervenant', $Consultant)
                        ->setParameter('Type', $Type)
                        ->setParameter('Mois', $Mois)
                        ->setParameter('Annee', $Annee)
                        ->getQuery()
                        ->getSingleScalarResult());
    }
    
    
    public function getRapportParTypeParClient($Type, $Client) {
 
        return $this->createQueryBuilder('r')
                        ->select('r')
                        ->from('TimSoftGeneralBundle:FeuilleDePresence' ,'f')
                        ->where('r.FeuilleDePresence = f.id')
                        ->andWhere('f.client = :Client')
                        ->andWhere('r.typeIntervention =  :Type')
                        ->setParameter('Client', $Client)
                        ->setParameter('Type', $Type)
                        ->getQuery()
                        ->getResult();
    }
    
     public function getRapportParTypeParConsultant($Type, $Consultant) {
 
        return $this->createQueryBuilder('r')
                        ->select('r')
                        ->from('TimSoftGeneralBundle:FeuilleDePresence' ,'f')
                        ->where('r.FeuilleDePresence = f.id')
                        ->andWhere('f.intervenant = :Consultant')
                        ->andWhere('r.typeIntervention =  :Type')
                        ->setParameter('Consultant', $Consultant)
                        ->setParameter('Type', $Type)
                        ->getQuery()
                        ->getResult();
    }
    
//    public function getRapportParTypeParConsultant($Type,$Consultant) {
//        
//       return $this->createQueryBuilder('r')
//                        ->select('r')
//                        ->from('TimSoftGeneralBundle:FeuilleDePresence' ,'f')
//                        ->where('r.FeuilleDePresence = f.id')
//                        ->andWhere('f.intervenant = :Consultant')
//                        ->andWhere('r.typeIntervention =  :Type')
//                        ->setParameter('Consultant', $Consultant)
//                        ->setParameter('Type', $Type)
//                        ->getQuery()
//                        ->getResult();
//    }
    
//    public function getRapportParTypeParClient($Type,$Clientt) {
//        
//        return  intval($this->createQueryBuilder('count(r)')
//                        ->select('r')
//                        ->from('TimSoftGeneralBundle:FeuilleDePresence' ,'f')
//                        ->where('r.FeuilleDePresence = f.id')
//                        ->andWhere('f.client = :Client')
//                        ->andWhere('r.typeIntervention =  :Type')
//                        ->setParameter('Client', $Clientt)
//                        ->setParameter('Type', $Type)
//                        ->getQuery()
//                        ->getSingleScalarResult());
//    }
    
//    
//     public function getNBRapportParValidationParClient($validation, $Client) {
// 
//        return intval($this->createQueryBuilder('r')
//                        ->select('COUNT(r)')
//                        ->from('TimSoftGeneralBundle:FeuilleDePresence' ,'f')
//                        ->where('r.FeuilleDePresence = f.id')
//                        ->andWhere('r.confirmationDeInterventionParClient =  :validation')
//                        ->setParameter('validation', $validation)
//                        ->setParameter('Client', $Client)
//                        ->getQuery()
//                        ->getSingleScalarResult());
//    }
}
