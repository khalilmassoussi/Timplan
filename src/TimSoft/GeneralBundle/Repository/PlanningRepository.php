<?php

namespace TimSoft\GeneralBundle\Repository;

/**
 * Created by PhpStorm.
 * User: Walid
 * Date: 11/09/2019
 * Time: 09:49
 */
class PlanningRepository extends \Doctrine\ORM\EntityRepository
{

    public function dropEvent($idEvent, $startDate, $endDate, $allDay)
    {
        return $this
            ->createQueryBuilder('e')
            ->update('TimSoftGeneralBundle:Planning', 'e')
            ->set('e.start', '?1')
            ->set('e.end', '?2')
            ->set('e.allDay', '?3')
            ->where('e.id = ?4')
            ->setParameter(1, $startDate)
            ->setParameter(2, $endDate)
            ->setParameter(3, $allDay)
            ->setParameter(4, $idEvent)
            ->getQuery()
            ->getResult();
    }

    public function resizeEvent($idEvent, $startDate, $endDate)
    {
        return $this
            ->createQueryBuilder('e')
            ->update('TimSoftGeneralBundle:Planning', 'e')
            ->set('e.start', '?1')
            ->set('e.end', '?2')
            ->where('e.id = ?3')
            ->setParameter(1, $startDate)
            ->setParameter(2, $endDate)
            ->setParameter(3, $idEvent)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $user
     * @param $start
     * @param $end
     * @param null $currentUser
     * @return mixed
     */
    public function findByUser($user, $start, $end, $currentUser = null)
    {

        if ($currentUser->getEmail() != 'f.dridi@timsoft.com.tn') {
            $query = $this->createQueryBuilder('p')
                ->select('p')
                ->leftJoin('p.accompagnements', 'u')
                ->where('p.utilisateur = :user')
                ->orWhere('u = :user')
                ->andwhere('p.start BETWEEN :start AND :end')
                ->andWhere('p.end BETWEEN :start AND :end')
                ->andWhere('DATE_DIFF(CURRENT_TIMESTAMP(), p.end) <= 2 OR p.statut = :termine')
                ->setParameter('start', $start)
                ->setParameter('end', $end)
                ->setParameter('user', $user)
                ->setParameter('termine', "Terminé")
                ->getQuery()
                ->getResult();
            return $query;
        } else {
            return $this->createQueryBuilder('p')
                ->select('p')
                ->leftJoin('p.accompagnements', 'u')
                ->where('p.utilisateur = :user')
                ->orWhere('u = :user')
                ->andwhere('p.start BETWEEN :start AND :end')
                ->andWhere('p.end BETWEEN :start AND :end')
                ->setParameter('start', $start)
                ->setParameter('end', $end)
                ->setParameter('user', $user)
                ->getQuery()
                ->getResult();
        }

    }

    public function findByUserBus($bus)
    {
        return $this->createQueryBuilder('u')
            ->select('u')
            ->leftJoin('u.utilisateur', 'b')
            ->leftJoin('b.bus', 'i')
            ->where(':bu MEMBER OF b.bus')
            ->setParameter('bu', $bus)
            ->getQuery()
            ->getResult();
    }

    public function getConfirméByUser($user)
    {
        if ($user->getEmail() != 'f.dridi@timsoft.com.tn') {
            if ($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_GESTIONNAIRE')) {
                return $this->createQueryBuilder('p')
                    ->select('p')
                    ->andWhere('p.statut = :statut')
                    ->andWhere('feuille is null')
                    ->andWhere('DATE_DIFF(CURRENT_TIMESTAMP(), p.end) <= 2 OR p.statut = :termine')
                    ->setParameter('termine', "Terminé")
                    ->leftJoin('p.feuille', 'feuille')
                    ->setParameter('statut', "Confirmé")
//                    ->orderBy('p.id', 'DESC')
                    ->getQuery()
                    ->getResult();
            }
            return $this->createQueryBuilder('p')
                ->select('p')
                ->where('p.utilisateur = :user')
                ->andWhere('p.statut = :statut')
                ->andWhere('feuille is null')
                ->andWhere('DATE_DIFF(CURRENT_TIMESTAMP(), p.end) <= 2 OR p.statut = :termine')
                ->setParameter('termine', "Terminé")
                ->leftJoin('p.feuille', 'feuille')
                ->setParameter('statut', "Confirmé")
                ->setParameter('user', $user)
//            ->orderBy('p.id DESC')
                ->getQuery()
                ->getResult();
        } else {
            if ($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_GESTIONNAIRE')) {
                return $this->createQueryBuilder('p')
                    ->select('p')
                    ->andWhere('p.statut = :statut')
                    ->andWhere('feuille is null')
                    ->leftJoin('p.feuille', 'feuille')
                    ->setParameter('statut', "Confirmé")
                    ->orderBy('p.id', 'DESC')
                    ->getQuery()
                    ->getResult();
            }
            return $this->createQueryBuilder('p')
                ->select('p')
                ->where('p.utilisateur = :user')
                ->andWhere('p.statut = :statut')
                ->andWhere('feuille is null')
                ->leftJoin('p.feuille', 'feuille')
                ->setParameter('statut', "Confirmé")
                ->setParameter('user', $user)
//            ->orderBy('p.id DESC')
                ->getQuery()
                ->getResult();
        }
    }

    public function PlanningOfMonth($id)
    {
        $month = new \DateTime();

        return $this->createQueryBuilder('p')
            ->select('p')
            ->where('p.utilisateur = :id')
            ->andWhere('MONTH(p.start) = MONTH(:month)')
            ->setParameter('month', $month)
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }

    public function PlanningOfLastWeek()
    {
        $previous_week = strtotime("-1 week +1 day");

        $start_week = strtotime("monday midnight", $previous_week);
        $end_week = strtotime("next friday", $start_week);

        $start_week = date("Y-m-d", $start_week);
        $end_week = date("Y-m-d", $end_week);

        return $this->createQueryBuilder('p')
            ->select('p')
            ->where('p.start BETWEEN :monday AND :sunday')
//            ->andWhere('p.statut like :terminer')
//            ->setParameter('terminer', 'Terminer')
            ->setParameter('monday', $start_week)
            ->setParameter('sunday', $end_week)
            ->getQuery()
            ->getResult();
    }

    public function getByClient($id, $start, $end, $utilisateur = null)
    {
        if ($utilisateur && $utilisateur->getEmail() != 'f.dridi@timsoft.com.tn') {
            $query = $this->createQueryBuilder('p')
                ->select('p')
                ->leftJoin('p.lc', 'lc')
                ->leftJoin('lc.commande', 'c')
                ->where('c.client = :id')
                ->andwhere('p.start BETWEEN :start AND :end')
                ->andWhere('p.end BETWEEN :start AND :end')
                ->andWhere('DATE_DIFF(CURRENT_TIMESTAMP(), p.end) <= 2 OR p.statut = :termine')
                ->setParameter('start', $start)
                ->setParameter('end', $end)
                ->setParameter('termine', "Terminé")
                ->setParameter('id', $id)
                ->getQuery()
                ->getResult();
            return $query;
        } else {
            return $this->createQueryBuilder('p')
                ->select('p')
                ->leftJoin('p.lc', 'lc')
                ->leftJoin('lc.commande', 'c')
                ->where('c.client = :id')
                ->andwhere('p.start BETWEEN :start AND :end')
                ->andWhere('p.end BETWEEN :start AND :end')
                ->setParameter('start', $start)
                ->setParameter('end', $end)
                ->setParameter('id', $id)
                ->getQuery()
                ->getResult();
        }
    }

    public function countByStatut($user, $statut, $date)
    {
        return $this->createQueryBuilder('p')
            ->select('count(p)')
            ->where('MONTH(p.start) = :date')
            ->andWhere('p.statut = :statut')
            ->andWhere('p.utilisateur = :utilisateur')
            ->setParameter('date', $date->format('m'))
            ->setParameter('statut', $statut)
            ->setParameter('utilisateur', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function planning15Jours($utilisateur)
    {
        $date = new \DateTime();
        $interval = new \DateInterval('P15D');
        $date->sub($interval);
        return $this->createQueryBuilder('p')
            ->select('p')
            ->where('p.start >= :date')
            ->andWhere('p.utilisateur = :utilisateur')
            ->setParameter('utilisateur', $utilisateur)
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();
    }

    public function ifExist($allDay, $start, $end)
    {
        if ($allDay == 1) {
            return $this->createQueryBuilder('p')
                ->select('p')
                ->where('p.allDay = :allDay')
                ->setParameter('allDay', $allDay)
                ->andWhere('p.start = :start')
                ->setParameter('start', $start)
                ->andWhere('p.end = :end')
                ->setParameter('end', $end)
                ->getQuery()
                ->getResult();
        } else {
            $startDate = $start->format('Y-m-d');
            $startTime = $start->format('H:i');
            $endDate = $end->format('Y-m-d');
            $endTime = $end->format('H:i');
            return $this->createQueryBuilder('p')
                ->select('p')
                ->Where('DATE(p.start) >= :start')
                ->setParameter('start', $startDate)
                ->andWhere('DATE(p.end) <= :end')
                ->setParameter('end', $endDate)
                ->andWhere('')
                ->getQuery()
                ->getResult();
        }
    }

    public function betweenDates($start, $end, $utilisateur = null)
    {
        if ($utilisateur->getEmail() != 'f.dridi@timsoft.com.tn') {
            $query = $this->createQueryBuilder('p')
                ->select('p')
                ->andwhere('p.start BETWEEN :start AND :end')
                ->andWhere('p.end BETWEEN :start AND :end')
                ->andWhere('DATE_DIFF(CURRENT_TIMESTAMP(), p.end) <= 2 OR p.statut = :termine')
                ->setParameter('start', $start)
                ->setParameter('end', $end)
                ->setParameter('termine', "Terminé")
                ->getQuery()
                ->getResult();
            return $query;
        } else {
            return $this->createQueryBuilder('p')
                ->select('p')
                ->where('p.start BETWEEN :start AND :end')
                ->andWhere('p.end BETWEEN :start AND :end')
                ->setParameter('start', $start)
                ->setParameter('end', $end)
                ->getQuery()
                ->getResult();
        }
    }
}

