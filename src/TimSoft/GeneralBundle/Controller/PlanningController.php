<?php

namespace TimSoft\GeneralBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use TimSoft\CommandeBundle\Entity\PreTeteCommande;
use TimSoft\GeneralBundle\Entity\NotificationUtilisateur;
use TimSoft\GeneralBundle\Entity\Planning;
use TimSoft\NotificationBundle\Entity\Notification;

class PlanningController extends Controller
{
    /**
     * @param $user
     * @param Request $request
     * @return JsonResponse
     */
    public function listAction($user, Request $request)
    {
        $plannings = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Planning')->findByUser($user, $request->get('start'), $request->get('end'));
        return new JsonResponse($plannings);
    }

    /**
     * Edite un évenement par glissement
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function dropEventAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            if ($request->get('User') == 'null') {
                $planning = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Planning')->find($request->get('id'));
                $user = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Utilisateur')->find($planning->getUtilisateur());
            } else {
                $user = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Utilisateur')->find($request->get('User'));
            }
            $planning = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Planning')->find($request->get('id'));
            $startDate = $request->request->get('start');
            $endDate = $request->request->get('end');
            $date = new \DateTime($startDate);
            $end = new \DateTime($endDate);
            $ev = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Planning')->findByUtilisateur($user);
            $exist = false;
            $setAllDay = true;
            $count = 0;
            foreach ($ev as $value) {
                if ($planning->isAllDay() || $value->isAllDay()) {
                    if ($value->getStart()->format('Y-m-d') == $value->getEnd()->format('Y-m-d') && $value->getStart()->format('Y-m-d') == $date->format('Y-m-d')) {
                        $exist = true;
                    }
                    if ($value->getStart()->format('Y-m-d') <= $date->format('Y-m-d') && $value->getEnd()->format('Y-m-d') > $end->format('Y-m-d')) {
                        $exist = true;

                    }
//                    if ($value->getStart()->format('Y-m-d') <= $date->format('Y-m-d') && $value->jRestantes() == 1 && $value->getEnd()->format('Y-m-d') >= $end->format('Y-m-d')) {
//                        $exist = true;
//                        //  return new Response(json_encode($value->jRestantes()), 419);
//                    }
                }
                if ($value->getStart()->format('Y-m-d') == $value->getEnd()->format('Y-m-d') && $value->getStart()->format('Y-m-d') == $date->format('Y-m-d') || $value->getStart()->format('Y-m-d') <= $date->format('Y-m-d') && $value->getEnd()->format('Y-m-d') > $end->format('Y-m-d')) {
                    $count++;
                }
            }
            // return new Response(json_encode($exist), 419);
            if ($exist || $count == 2) {
                return new Response(json_encode($exist), 419);
            } else {
                // return new Response(json_encode($exist), 419);
                $idEvent = $request->request->get('id');
                $allDay = $request->request->get('all_day');
                $bool = filter_var($allDay, FILTER_VALIDATE_BOOLEAN);
                $em = $this->getDoctrine()->getManager();
                if ($request->get('User') == 'null') {
                    $rst = $em->getRepository('TimSoftGeneralBundle:Planning')->dropEvent($idEvent, $startDate, $endDate, $bool);
                    return new Response($request->request->get('all_day'));
                } else {
                    $planning->setStart($date);
                    $planning->setEnd($date);
                    $planning->setUtilisateur($user);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($planning);
                    $em->flush();
                    return new Response('success');
                }

            }
        }
        return new Response('erreur');
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function addEventAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $startDate = $request->get('start');
            $endDate = $request->get('end');
            $idUser = $request->get('idUser');
            $ligneCommande = $request->get('lc');
            $user = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Utilisateur')->find($idUser);
            $ligneC = $this->getDoctrine()->getRepository('TimSoftCommandeBundle:LigneCommande')->find($ligneCommande);
            $date = new \DateTime($startDate);
            $end = new \DateTime($endDate);
            $ev = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Planning')->findByUtilisateur($user);
            $exist = false;
            $setAllDay = true;
            $Existant = null;
            $count = 0;
            foreach ($ev as $value) {
                if ($value->getStart()->format('Y-m-d') == $date->format('Y-m-d')) {
                    $count++;
                    if ($value->isAllDay()) {
                        $exist = true;
                    } else {
                        $setAllDay = false;
                        $Existant = $value;
                    }
                }
            }
            if (($exist || $count == 2) && (!$this->getUser()->hasRole('ROLE_ADMIN') && !$this->getUser()->hasRole('ROLE_GESTIONNAIRE') && !$this->getUser()->hasRole('ROLE_CHEF'))) {
                return new Response(json_encode($ev), 419);
            } else {
                $planning = new Planning($request->get('title'), $date, $end);
                $planning->setStatut('Proposé');
                $planning->setUtilisateur($user);
                if ($setAllDay) {
                    $planning->setAllDay($setAllDay);
                } else {
                    $planning->setAllDay(false);
                    if ($Existant->getStart()->format('H') > 12) {
                        $planning->setStart($date->add(new \DateInterval('PT08H30M')));
                    } elseif ($Existant->getStart()->format('H') < 12) {
                        $planning->setStart($date->add(new \DateInterval('PT14H00M')));
                    }
                }
                if ($ligneC->getMontantHT() == 0) {
                    $planning->setFacturation('Gratuit');
                } else {
                    $planning->setFacturation('Facturé');
                }
                if ($ligneC->JRestant() == 0.5) {
                    $planning->setStart($date->add(new \DateInterval('PT08H30M')));
                    $planning->setAllDay(false);
                }
                $planning->setLc($ligneC);
                $em = $this->getDoctrine()->getManager();
                $plannings = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Planning')->findByLc($ligneC->getId());
                $qte = 0;
                foreach ($plannings as $plan) {
                    $qte = $qte + $plan->jRestantes();
                }
                if ($request->get('JSup')) {
                    $planning->setJSupplementaire(true);
                }
                $em->persist($planning);
                $notification = new Notification();
                $notification
                    ->setTitle('Intervention Planifiée')
                    ->setDescription('Client: ' . $planning->getLc()->getCommande()->getClient()->getRaisonSociale())
                    ->setResponsable($this->getUser())
                    ->setRoute('GetMyPlans')
                    ->setParameters(array('id' => $planning->getUtilisateur()->getId()));
                $em->persist($notification);
                $em->flush();
                $notificationUser = new NotificationUtilisateur();
                $notificationUser
                    ->setNotification($notification)
                    ->setUtilisateur($user)
                    ->setVu(0);
                $em->persist($notificationUser);
                $em->flush();
                $pusher = $this->get('mrad.pusher.notificaitons'); // Appel le service
                $pusher->trigger($notificationUser);
            }
            return new JsonResponse($planning);
        }
    }

    public function resizeEventAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {

            $idEvent = $request->request->get('id');
            $startDate = $request->request->get('start');
            $endDate = $request->request->get('end');
            $em = $this->getDoctrine()->getManager();
            $ev = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Planning')->find($request->get('id'));
            $end = $ev->getEnd();
            $ev->setEnd(new \DateTime($endDate));
            $em->merge($ev);
            $em->flush();
            // $rst = $em->getRepository('TimSoftGeneralBundle:Planning')->resizeEvent($idEvent, $startDate, $endDate);
            $plannings = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Planning')->findByLc($ev->getLc()->getId());
            if ($plannings) {
                $somme = 0;
                foreach ($plannings as $planning) {
                    $somme = $somme + $planning->jRestantes();
                    $quantite = $planning->getLc()->getQuantite();
                }
                $rest = $quantite - $somme;
                if ($rest < 0 && $planning->isAllDay()) {
                    $rst = $em->getRepository('TimSoftGeneralBundle:Planning')->resizeEvent($idEvent, $startDate, $end);
                    // $content = json_encode(array('message' => $end));
                    return new Response("", 419);
                }
            }
        }
        return new JsonResponse($plannings);
    }

    public function mesPlansAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        return $this->render('@TimSoftCommande/Default/mesplans.html.twig');
    }

    public function listPlansAction(Request $request)
    {
        $plannings = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Planning')->findByUser($request->get('id'));
        return new JsonResponse($plannings);
    }

    public function plansAction(Request $request)
    {
        $plannings = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Planning')->find($request->get('id'));
        return new JsonResponse($plannings);
    }

    public function calculateAction(Request $request)
    {
        $plannings = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Planning')->findByLc($request->get('id'));
        $lc = $this->getDoctrine()->getRepository('TimSoftCommandeBundle:LigneCommande')->find($request->get('id'));
        if ($plannings) {
            $somme = 0;
            $quantite = null;
            foreach ($plannings as $planning) {
                if ($planning->getStatut() != 'Terminé') {
                    $somme = $somme + $planning->jRestantes();
                }
                $quantite = $planning->getLc()->getQteRestante();

            }
            $rest = $quantite - $somme;
            return new JsonResponse(floatval($rest));
        }
        return new JsonResponse(floatval($lc->getQteRestante()));
    }

    public function BuPlanAction()
    {
        return $this->render('@TimSoftGeneral/Planning/list.html.twig');
    }

    public function ByBuAction()
    {
        $user = $this->getUser();
        $plannings = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Planning')->findByUserBus($user->getBus());
        return new JsonResponse($plannings);
    }

    public function modificationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $planning = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Planning')->find($request->get('id'));
        $oldStart = $planning->getStart();
        $oldEnd = $planning->getEnd();
//        if ($request->get('allday') == 'true') {
//            $planning->setAllDay(true);
//        } else {
//            $planning->setAllDay(false);
//        }
        $count = 0;
        $exist = false;
        $setAllDay = true;
        if ($request->get('start')) {
            $idf = new \IntlDateFormatter('fr_Fr', \IntlDateFormatter::FULL, \IntlDateFormatter::NONE);
            if ($planning->isAllDay()) {
                $idf->setPattern('dd MMMM yyyy');
            } else {
                $idf->setPattern('dd MMMM yyyy');
            }
            $tstp = $idf->parse($request->get('start'));
            $dt = (new \DateTime())->setTimestamp($tstp);
            //  return new JsonResponse(/$oldStart);
            //  return new JsonResponse('9ad9ad');
            $ev = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Planning')->findByUtilisateur($planning->getUtilisateur());
            //   $aa = [];
            foreach ($ev as $value) {
                if ($value->getStart()->format('Y-m-d') == $dt->format('Y-m-d')) {
                    //  return new JsonResponse($value);
                    if (($value->isAllDay()) && ($value->getId() != $planning->getId())) {
                        $exist = true;
//                        $count = $planning->getId() . ' ' . $value->getId();
                    } elseif (!$value->isAllDay() && $value->getId() != $planning->getId()) {
                        $setAllDay = false;
                    }
                }
//                if ($value->getStart() <= $dt && $value->getEnd() > $dt) {
//                    $setAllDay = false;
//                    $exist = true;
//                    //   $aa[] = $date;
//                    // return new Response(json_encode($exist), 419);
//                }
            }
            $planning->setStart($dt);
            $planning->setEnd($dt);
        }
//        if ($request->get('end')) {
//            $idf = new \IntlDateFormatter('fr_Fr', \IntlDateFormatter::FULL, \IntlDateFormatter::NONE);
//            if ($planning->isAllDay()) {
//                $idf->setPattern('dd MMMM yyyy');
//            } else {
//                $idf->setPattern('dd MMMM yyyy');
//            }
//            $tstp = $idf->parse($request->get('start'));
//            $dt = (new \DateTime())->setTimestamp($tstp);
////            $planning->setStart($dt);
//            $planning->setEnd($dt);
////
//        }
        if ($exist && (!$this->getUser()->hasRole('ROLE_ADMIN') && !$this->getUser()->hasRole('ROLE_GESTIONNAIRE') && !$this->getUser()->hasRole('ROLE_CHEF'))) {
            //   return new JsonResponse($exist);
            return new JsonResponse('Erreur!', 419);
        } else {
//            return new JsonResponse($count);
            if ($request->get('allday') == 'Midi') {
                $planning->setAllDay(false);
                $planning->getStart()->setTime(14, 30);
                $planning->getEnd()->setTime(18, 00);

            } elseif ($request->get('allday') == 'Matin') {
                $planning->setAllDay(false);
                $planning->getStart()->setTime(8, 30);
                $planning->getEnd()->setTime(13, 00);
            } else {
                if ($setAllDay == false) {
                    // $planning->setAllDay(false);
                    return new Response('error', 419);
                } else {
                    $planning->setAllDay(true);
                }
            }
            // $planning->setAllDay($setAllDay);
            $planning->setFacturation($request->get('facturation'));

            if ($request->get('lieu') == 'SurSite') {
                $planning->setLieu('Sur Site');
            } else {
                $planning->setLieu('à distance');

            }
            if ($request->get('accompagnements') == 'Non') {
                $planning->clearAll();
            }
            $message = (new \Swift_Message('Planning ' . $request->get('statut') . ': ' . $planning->getLc()->getCommande()->getClient()->getRaisonSociale() . ' du ' . $planning->getStart()->format('d/m/Y')))
                ->setFrom(['timplan@timsoft.net' => "Administrateur TimSoft"]);
            $messageG = (new \Swift_Message('Planning ' . $request->get('statut') . ': ' . $planning->getLc()->getCommande()->getClient()->getRaisonSociale() . ' du ' . $planning->getStart()->format('d/m/Y')))
                ->setFrom(['timplan@timsoft.net' => "Administrateur TimSoft"]);
            $failedRecipients = [];
            $numSent = 0;
            $user = $planning->getUtilisateur();
            $message->addTo($user->getEmail(), $user->getNomUtilisateur() . ' ' . $user->getPrenomUtilisateur());

            $gestionnaires = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Utilisateur')->findByRoleUtilisateur('ROLE_GESTIONNAIRE');
//            $buManagers = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Utilisateur')->findByRoleUtilisateur('ROLE_CHEF');
//            foreach ($buManagers as $buManager) {
//                $bu = $this->getDoctrine()->getRepository('TimSoftBuBundle:Bu')->findOneBy(array('libelle' => $planning->getLc()->getBu()));
//                foreach ($buManager->getBus() as $bus) {
//                    if ($bu == $bus) {
//                        $message->addTo($buManager->getEmail(), $buManager->getNomUtilisateur() . ' ' . $buManager->getPrenomUtilisateur());
//                    }
//                }
//            }
            if ($planning->getAccompagnements()) {
                foreach ($planning->getAccompagnements() as $bus) {
                    $message->addTo($bus->getEmail(), $bus->getNomUtilisateur() . ' ' . $bus->getPrenomUtilisateur());
                }

            }

            if ($request->get('userClient') != 'null' && $request->get('statut') == 'Confirmé') {
                $user = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Utilisateur')->find($request->get('userClient'));
                $planning->setConfirmePar($user);
                $message->addCc($user->getEmail(), $user->getNomUtilisateur() . ' ' . $user->getPrenomUtilisateur());

            } else {
                $planning->setConfirmePar(null);

            }

            $planning->setStatut($request->get('statut'));

            if ($planning->getStatut() == 'Confirmé') {
                $from_name = "Administrateur TimSoft";
                $from_address = "timplan@timsoft.net";
                $to_name = $user->getPrenomUtilisateur() . ' ' . $user->getNomUtilisateur();
                $to_address = $user->getEmail();
                if ($planning->isAllDay()) {
                    $startTime = $planning->getStart()->format('m/d/Y 07:30');
                    $endTime = $planning->getStart()->format('m/d/Y 17:00');
//                    return new Response($startTime);
                } else {
                    $startTime = $planning->getStart()->format('m/d/Y H:i');
                    $endTime = $planning->getEnd()->format('m/d/Y H:i');
                    // return new JsonResponse($startTime->getTimestamp());
                }
//                $startTime = $planning->getStart();
//                return new JsonResponse($startTime->getTimestamp());

                $subject = $planning->getTitle();
//                $description = "My Awesome Description";
                $location = $planning->getLieu();
                $domain = 'timplan.timsoft.net';
                $ical = 'BEGIN:VCALENDAR' . "\r\n" .
                    'PRODID:-//Microsoft Corporation//Outlook 10.0 MIMEDIR//EN' . "\r\n" .
                    'VERSION:2.0' . "\r\n" .
                    'METHOD:REQUEST' . "\r\n" .
//                    'BEGIN:VTIMEZONE' . "\r\n" .
//                    'TZID:Europe/Paris' . "\r\n" .
                    'BEGIN:STANDARD' . "\r\n" .
                    'DTSTART:20091101T020000' . "\r\n" .
                    'RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=1SU;BYMONTH=11' . "\r\n" .
                    'TZOFFSETFROM:-0400' . "\r\n" .
                    'TZOFFSETTO:-0500' . "\r\n" .
//                    'TZNAME:EST' . "\r\n" .
                    'END:STANDARD' . "\r\n" .
                    'BEGIN:DAYLIGHT' . "\r\n" .
                    'DTSTART:20090301T020000' . "\r\n" .
                    'RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=2SU;BYMONTH=3' . "\r\n" .
                    'TZOFFSETFROM:-0500' . "\r\n" .
                    'TZOFFSETTO:-0400' . "\r\n" .
                    'TZNAME:EDST' . "\r\n" .
                    'END:DAYLIGHT' . "\r\n" .
//                    'END:VTIMEZONE' . "\r\n" .
                    'BEGIN:VEVENT' . "\r\n" .
                    'ORGANIZER;CN="' . $from_name . '":MAILTO:' . $from_address . "\r\n" .
                    'ATTENDEE;CN="' . $to_name . '";ROLE=REQ-PARTICIPANT;RSVP=TRUE:MAILTO:' . $to_address . "\r\n" .
                    'LAST-MODIFIED:' . date("Ymd\TGis") . "\r\n" .
                    'UID:' . date("Ymd\TGis", strtotime($startTime)) . rand() . "@" . $domain . "\r\n" .
                    'DTSTAMP:' . date("Ymd\TGis") . "\r\n" .
                    'DTSTART:' . date("Ymd\THis", strtotime($startTime)) . "\r\n" .
                    'DTEND:' . date("Ymd\THis", strtotime($endTime)) . "\r\n" .
                    'TRANSP:OPAQUE' . "\r\n" .
                    'SEQUENCE:1' . "\r\n" .
                    'SUMMARY:' . $subject . "\r\n" .
                    'LOCATION:' . $location . "\r\n" .
                    'CLASS:PUBLIC' . "\r\n" .
                    'PRIORITY:5' . "\r\n" .
                    'BEGIN:VALARM' . "\r\n" .
                    'TRIGGER:-PT15M' . "\r\n" .
                    'ACTION:DISPLAY' . "\r\n" .
                    'DESCRIPTION:Reminder' . "\r\n" .
                    'END:VALARM' . "\r\n" .
                    'END:VEVENT' . "\r\n" .
                    'END:VCALENDAR' . "\r\n";

                $attachment = \Swift_Attachment::newInstance()
                    ->setFilename("invite.ics")
                    ->setContentType('text/calendar;charset=UTF-8;name="invite.ics";method=REQUEST')
                    ->setBody($ical)
                    ->setDisposition('inline;filename=invite.ics');
                $message->attach($attachment);
            }
            if ($planning->getFeuille()) {
                $planning->getFeuille()->setDateIntervention($planning->getStart());
                if ($request->get('statut') === 'null') {
                    $planning->setStatut('Terminé');
                }
                $em->persist($planning->getFeuille());
                $em->flush();
            }
            $planning->setCommentaire($request->get('commentaire'));
            $message->setBody(
                $this->renderView('@TimSoftCommande/Default/email/intervention.txt.twig', array('planning' => $planning)), 'text/html');
            $messageG->setBody(
                $this->renderView('@TimSoftCommande/Default/email/intervention.txt.twig', array('planning' => $planning)), 'text/html');
            $numSent += $this->get('mailer')->send($message, $failedRecipients);
            $messageG->addTo('w.benmustapha@timsoft.com.tn', 'Wafa Benmustapha');
            $messageG->addTo('f.cherif@timsoft.com.tn', 'Fatma CHERIF');
            $numSent += $this->get('mailer')->send($messageG, $failedRecipients);

            $em->persist($planning);

            $em->flush();
            return new JsonResponse($planning);
        }
    }

    public function accompagnementAction(Request $request)
    {

        $planning = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Planning')->find($request->get('id'));
        $user = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Utilisateur')->find($request->get('idu'));
        $em = $this->getDoctrine()->getManager();
        if ($request->get('type') == 'selecting') {
            $planning->addAccomp($user);
        } elseif ($request->get('type') == 'unselect') {
            $planning->removeAccomp($user);
        }
        $em->persist($planning);
        $em->flush();
        return new JsonResponse($request->get('type'));
    }

    public function getByUserAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        if ($this->getUser()->hasRole('ROLE_ADMIN')) {
            $planning = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Planning')->getConfirméByUser($this->getUser());
        } else {
            $planning = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Planning')->getConfirméByUser($this->getUser());
        }
        return $this->render('@TimSoftGeneral/Planning/redactionFP.html.twig', array('plannings' => $planning));
    }

    public function getByBuAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $bus = $this->getDoctrine()->getRepository('TimSoftBuBundle:Bu')->findAll();
        return $this->render('@TimSoftCommande/Default/BuPlanning.html.twig', array('bus' => $bus));
    }

    public function allPlansAction(Request $request)
    {
        $plannings = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Planning')->betweenDates($request->get('start'), $request->get('end'));
        return new JsonResponse($plannings);
    }

    public function deleteAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $planning = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Planning')->find($request->get('id'));
        $lc = $planning->getLc();
        if ($planning->getStatut() == 'Terminé') {
            $newQte = $lc->getQteRestante() + $planning->jRestantes();
            $lc->setQteRestante($newQte);
            $em->persist($lc);
        }
        $this->getDoctrine()->getManager()->remove($planning);
        $this->getDoctrine()->getManager()->flush();
        return new JsonResponse("Success");
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function telechargerPlanningAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

//        return new JsonResponse($request->get('cc'));
// --------------------------------------------------------------------------
        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
        $plannings = [];
        foreach ($request->get('ids') as $id) {
            $planning = $em->getRepository("TimSoftGeneralBundle:Planning")->find($id);
            $plannings[] = $planning;
        }
        //  return new JsonResponse($plannings);
//        $plannings = $em->getRepository("TimSoftGeneralBundle:Planning")->PlanningOfMonth($this->getUser()->getId());
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
        $date = new \DateTime();
        $start = new \DateTime($request->get('start'));

        $end = new \DateTime($request->get('end'));
        $phpExcelObject->getProperties()->setCreator($this->getUser()->getPrenomUtilisateur() . ' ' . $this->getUser()->getNomUtilisateur())
            ->setTitle('Planning du ' . $start->format('d/m') . ' au ' . $end->format('d/m'))
            ->setSubject('Objet');

        $sheet = $phpExcelObject->setActiveSheetIndex(0);

        $sheet->setCellValue('A1', 'Client');
        $sheet->setCellValue('B1', 'Libelle');
        $sheet->setCellValue('C1', 'Date');
        $sheet->setCellValue('D1', 'N°Commande');
        $sheet->setCellValue('E1', 'Invtervenant');
        $sheet->setCellValue('F1', 'Lieu');
        $sheet->setCellValue('G1', 'Ressource D\'accompagnement');
        $sheet->setCellValue('H1', 'Facturation');
        $sheet->setCellValue('I1', 'Statut');
        $sheet->setCellValue('J1', 'Durée');


        $counter = 2;
        foreach ($plannings as $planning) {
            if ($planning->getLc()->getCommande() instanceof PreTeteCommande) {
                $nCommande = 'Pré ' . $planning->getLc()->getCommande()->getNCommande();
            } else {
                $nCommande = $planning->getLc()->getCommande()->getNCommande();
            }
            $string = null;
            foreach ($planning->getAccompagnements() as $accompagnement) {
                $string = $string . ',' . $accompagnement;
            }
            $sheet->setCellValue('A' . $counter, $planning->getLc()->getCommande()->getClient()->getRaisonSociale());
            $sheet->setCellValue('B' . $counter, $planning->getLc()->getLibelle());
            $sheet->setCellValue('C' . $counter, $planning->getStart());
            $sheet->setCellValue('D' . $counter, $nCommande);
            $sheet->setCellValue('E' . $counter, $planning->getUtilisateur()->getPrenomUtilisateur() . ' ' . $planning->getUtilisateur()->getNomUtilisateur());
            $sheet->setCellValue('F' . $counter, $planning->getLieu());
            $sheet->setCellValue('G' . $counter, $string);
            $sheet->setCellValue('H' . $counter, $planning->getFacturation());
            $sheet->setCellValue('I' . $counter, $planning->getStatut());
            $sheet->setCellValue('J' . $counter, $planning->jRestantes());
            $counter++;
        }

        $phpExcelObject->getActiveSheet()->setTitle('PlanningSheet');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'Planning du ' . $start->format('d-m') . ' au ' . $end->format('d-m') . '.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;

// -------------------------------------------------------------

//        $view = $this->renderView('@TimSoftCommande/Default/Telechargement/planning.html.twig', array('plannings' => $plannings));
//// On crée une  instance pour définir les options de notre fichier pdf
//        $options = new Options();
//        // Pour simplifier l'affichage des images, on autorise dompdf à utiliser
//        // des  url pour les nom de  fichier
//        $options->set('isRemoteEnabled', true);
//        $d = new Dompdf($options);
//        $contxt = stream_context_create([
//            'ssl' => [
//                'verify_peer' => FALSE,
//                'verify_peer_name' => FALSE,
//                'allow_self_signed' => TRUE
//            ]
//        ]);
//        $d->setHttpContext($contxt);
//        $d->loadHtml($view);
//
//        // Render the HTML as PDF
//        $d->render();
//
//        $fileName = 'Planning ';
//        $fileName = str_replace('/', '', $fileName);
//        $d->stream($fileName, array(
//            'Attachment' => 0,
//        ));
//
//        // return $this->render('@TimSoftFeuilleRapportIntervention/Telechargement/TelechargerUneFeuillePresence.html.twig', array('Feuille' => $Feuille));
//        return new Response('', 200, array('Content-Type' => 'application/pdf'));
    }

    public function PlanningByClientAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $clients = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Client')->findAll();
        return $this->render('@TimSoftGeneral/Planning/PlanningByUser.html.twig', array('clients' => $clients));
    }

    public function jsonPlanningByClientAction($id, Request $request)
    {
        $plannings = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Planning')->getByClient($id, $request->get('start'), $request->get('end'));
        return new JsonResponse($plannings);
    }

    public function getByLcAction($id)
    {
        $plannings = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Planning')->findByLc($id);
        return new JsonResponse($plannings);
    }

    public function editAction(Request $request, Planning $planning)
    {
        $editForm = $this->createForm('TimSoft\GeneralBundle\Form\PlanningType', $planning);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $plannings = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Planning')->findByUtilisateur($planning->getUtilisateur());
            $plannings = $this->unsetValue($plannings, $planning, true);
            if (!$this->getUser()->hasRole('ROLE_ADMIN') && !$this->getUser()->hasRole('ROLE_GESTIONNAIRE') && !$this->getUser()->hasRole('ROLE_CHEF')) {
                foreach ($plannings as $plan) {
                    if (($plan->isAllDay() && $planning->isAllDay()) || (!$plan->isAllDay() && !$planning->isAllDay())) {
                        if ($this->getIntersection($plan->getStart(), $plan->getEnd(), $planning->getStart(), $planning->getEnd())) {
                            return new Response(json_encode(array('planExist' => true, $plan)));
                        }
                    } else {
                        if ($this->getIntersection($plan->getStart()->format('d-m-Y'), $plan->getEnd()->format('d-m-Y'), $planning->getStart()->format('d-m-Y'), $planning->getEnd()->format('d-m-Y'))) {
                            return new Response(json_encode(array('planExist' => true, $plan)));
                        }
                    }
                }
            }
            $temps = $editForm->get('temps')->getData();
            $feuille = $planning->getFeuille();
            if (in_array('Matin', $temps) && in_array('Après-midi', $temps)) {
                $planning->setAllDay(true);
                if ($feuille) {
                    $feuille->setHeureDebutInterventionMatin((new \DateTime())->setTime(8, 30));
                    $feuille->setHeureFinInterventionMatin((new \DateTime())->setTime(13, 00));
                    $feuille->setHeureDebutInterventionAM((new \DateTime())->setTime(14, 00));
                    $feuille->setHeureFinInterventionAM((new \DateTime())->setTime(18, 00));
                }
            } elseif (in_array('Matin', $temps) && !in_array('Après-midi', $temps)) {
                $planning->setAllDay(false);
                $planning->setStart($editForm->get('start')->getData());
                $planning->getStart()->setTime(8, 30);
                $planning->setEnd($editForm->get('end')->getData());
                $planning->getEnd()->setTime(13, 00);
                if ($feuille) {
                    $feuille->setHeureDebutInterventionMatin((new \DateTime())->setTime(8, 30));
                    $feuille->setHeureFinInterventionMatin((new \DateTime())->setTime(13, 00));
                    $feuille->setHeureDebutInterventionAM(null);
                    $feuille->setHeureFinInterventionAM(null);
                }
            } elseif (!in_array('Matin', $temps) && in_array('Après-midi', $temps)) {
                $planning->setAllDay(false);
                $planning->setStart($editForm->get('start')->getData());
                $planning->getStart()->setTime(14, 00);
                $planning->setEnd($editForm->get('end')->getData());
                $planning->getEnd()->setTime(18, 00);
                if ($feuille) {
                    $feuille->setHeureDebutInterventionMatin(null);
                    $feuille->setHeureFinInterventionMatin(null);
                    $feuille->setHeureDebutInterventionAM((new \DateTime())->setTime(14, 00));
                    $feuille->setHeureFinInterventionAM((new \DateTime())->setTime(18, 00));
                }
            }

            /* ---------------------------------- */
            $message = (new \Swift_Message('Planning ' . $request->get('statut') . ': ' . $planning->getLc()->getCommande()->getClient()->getRaisonSociale() . ' du ' . $planning->getStart()->format('d/m/Y')))
                ->setFrom(['timplan@timsoft.net' => "Administrateur TimSoft"]);
            $messageG = (new \Swift_Message('Planning ' . $request->get('statut') . ': ' . $planning->getLc()->getCommande()->getClient()->getRaisonSociale() . ' du ' . $planning->getStart()->format('d/m/Y')))
                ->setFrom(['timplan@timsoft.net' => "Administrateur TimSoft"]);
            $failedRecipients = [];
            $numSent = 0;
            $user = $planning->getUtilisateur();
            $message->addTo($user->getEmail(), $user->getNomUtilisateur() . ' ' . $user->getPrenomUtilisateur());
            if ($planning->getAccompagnements()) {
                foreach ($planning->getAccompagnements() as $bus) {
                    $message->addTo($bus->getEmail(), $bus->getNomUtilisateur() . ' ' . $bus->getPrenomUtilisateur());
                }
            }
            if ($planning->getStatut() == 'Confirmé') {
                $from_name = "Administrateur TimSoft";
                $from_address = "timplan@timsoft.net";
                $to_name = $user->getPrenomUtilisateur() . ' ' . $user->getNomUtilisateur();
                $to_address = $user->getEmail();
                if ($planning->isAllDay()) {
                    $startTime = $planning->getStart()->format('m/d/Y 07:30');
                    $endTime = $planning->getStart()->format('m/d/Y 17:00');
                } else {
                    $startTime = $planning->getStart()->format('m/d/Y H:i');
                    $endTime = $planning->getEnd()->format('m/d/Y H:i');
                }

                $subject = $planning->getTitle();
                $location = $planning->getLieu();
                $domain = 'timplan.timsoft.net';
                $ical = 'BEGIN:VCALENDAR' . "\r\n" .
                    'PRODID:-//Microsoft Corporation//Outlook 10.0 MIMEDIR//EN' . "\r\n" .
                    'VERSION:2.0' . "\r\n" .
                    'METHOD:REQUEST' . "\r\n" .
//                    'BEGIN:VTIMEZONE' . "\r\n" .
//                    'TZID:Europe/Paris' . "\r\n" .
                    'BEGIN:STANDARD' . "\r\n" .
                    'DTSTART:20091101T020000' . "\r\n" .
                    'RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=1SU;BYMONTH=11' . "\r\n" .
                    'TZOFFSETFROM:-0400' . "\r\n" .
                    'TZOFFSETTO:-0500' . "\r\n" .
//                    'TZNAME:EST' . "\r\n" .
                    'END:STANDARD' . "\r\n" .
                    'BEGIN:DAYLIGHT' . "\r\n" .
                    'DTSTART:20090301T020000' . "\r\n" .
                    'RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=2SU;BYMONTH=3' . "\r\n" .
                    'TZOFFSETFROM:-0500' . "\r\n" .
                    'TZOFFSETTO:-0400' . "\r\n" .
                    'TZNAME:EDST' . "\r\n" .
                    'END:DAYLIGHT' . "\r\n" .
//                    'END:VTIMEZONE' . "\r\n" .
                    'BEGIN:VEVENT' . "\r\n" .
                    'ORGANIZER;CN="' . $from_name . '":MAILTO:' . $from_address . "\r\n" .
                    'ATTENDEE;CN="' . $to_name . '";ROLE=REQ-PARTICIPANT;RSVP=TRUE:MAILTO:' . $to_address . "\r\n" .
                    'LAST-MODIFIED:' . date("Ymd\TGis") . "\r\n" .
                    'UID:' . date("Ymd\TGis", strtotime($startTime)) . rand() . "@" . $domain . "\r\n" .
                    'DTSTAMP:' . date("Ymd\TGis") . "\r\n" .
                    'DTSTART:' . date("Ymd\THis", strtotime($startTime)) . "\r\n" .
                    'DTEND:' . date("Ymd\THis", strtotime($endTime)) . "\r\n" .
                    'TRANSP:OPAQUE' . "\r\n" .
                    'SEQUENCE:1' . "\r\n" .
                    'SUMMARY:' . $subject . "\r\n" .
                    'LOCATION:' . $location . "\r\n" .
                    'CLASS:PUBLIC' . "\r\n" .
                    'PRIORITY:5' . "\r\n" .
                    'BEGIN:VALARM' . "\r\n" .
                    'TRIGGER:-PT15M' . "\r\n" .
                    'ACTION:DISPLAY' . "\r\n" .
                    'DESCRIPTION:Reminder' . "\r\n" .
                    'END:VALARM' . "\r\n" .
                    'END:VEVENT' . "\r\n" .
                    'END:VCALENDAR' . "\r\n";

                $attachment = \Swift_Attachment::newInstance()
                    ->setFilename("invite.ics")
                    ->setContentType('text/calendar;charset=UTF-8;name="invite.ics";method=REQUEST')
                    ->setBody($ical)
                    ->setDisposition('inline;filename=invite.ics');
                $message->attach($attachment);
            }
            if ($planning->getFeuille()) {
                $planning->getFeuille()->setDateIntervention($planning->getStart());
                $em->persist($planning->getFeuille());
            }
            $message->setBody(
                $this->renderView('@TimSoftCommande/Default/email/intervention.txt.twig', array('planning' => $planning)), 'text/html');
            $messageG->setBody(
                $this->renderView('@TimSoftCommande/Default/email/intervention.txt.twig', array('planning' => $planning)), 'text/html');
            $numSent += $this->get('mailer')->send($message, $failedRecipients);
            $messageG->addTo('w.benmustapha@timsoft.com.tn', 'Wafa Benmustapha');
            $messageG->addTo('f.cherif@timsoft.com.tn', 'Fatma CHERIF');
            $numSent += $this->get('mailer')->send($messageG, $failedRecipients);

            /* ----------------------------------------- */
            $this->getDoctrine()->getManager()->flush();
            return new Response(json_encode(array('status' => 'success')));
        }
        return $this->render('@TimSoftGeneral/Planning/editPlanning.html.twig', array(
            'planning' => $planning,
            'edit_form' => $editForm->createView(),
        ));
    }

    public function showAction(Planning $planning)
    {
        return $this->render('@TimSoftGeneral/Planning/showPlanning.html.twig', array(
            'planning' => $planning,
        ));
    }

    function getIntersection($startTime, $endTime, $chkStartTime, $chkEndTime)
    {
        if ($chkStartTime > $startTime && $chkEndTime < $endTime) {    #-> Check time is in between start and end time
            return true;
        } elseif (($chkStartTime > $startTime && $chkStartTime < $endTime) || ($chkEndTime > $startTime && $chkEndTime < $endTime)) {    #-> Check start or end time is in between start and end time
            return true;
        } elseif ($chkStartTime == $startTime || $chkEndTime == $endTime) {    #-> Check start or end time is at the border of start and end time
            return true;
        } elseif ($startTime > $chkStartTime && $endTime < $chkEndTime) {    #-> start and end time is in between  the check start and end time.
            return true;
        } else {
            return false;
        }
    }

    public function unsetValue(array $array, $value, $strict = TRUE)
    {
        if (($key = array_search($value, $array, $strict)) !== FALSE) {
            unset($array[$key]);
        }
        return $array;
    }

    public function newAction(Request $request)
    {
        $planning = new Planning($request->get('title'), new \DateTime($request->get('start')), new \DateTime($request->get('end')));
        if ($request->get('lc') && $request->get('utilisateur')) {
            $ligneC = $this->getDoctrine()->getRepository('TimSoftCommandeBundle:LigneCommande')->find($request->get('lc'));
            $planning->setLc($ligneC);
            $planning->setUtilisateur($this->getDoctrine()->getRepository('TimSoftGeneralBundle:Utilisateur')->find($request->get('utilisateur')));
        }
        $form = $this->createForm('TimSoft\GeneralBundle\Form\PlanningType', $planning, array());
        $form->handleRequest($request);
//        $user = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Utilisateur')->find($request->get('idUser'));
//        $planning->setUtilisateur($user);
//        var_dump($form);
//        die();
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $plannings = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Planning')->findByUtilisateur($planning->getUtilisateur());
            $plannings = $this->unsetValue($plannings, $planning, true);
            foreach ($plannings as $plan) {
                if (($plan->isAllDay() && $planning->isAllDay()) || (!$plan->isAllDay() && !$planning->isAllDay())) {
                    if ($this->getIntersection($plan->getStart(), $plan->getEnd(), $planning->getStart(), $planning->getEnd())) {
                        return new Response(json_encode(array('planExist' => true, $plan)));
                    }
                } else {
                    if ($this->getIntersection($plan->getStart()->format('d-m-Y'), $plan->getEnd()->format('d-m-Y'), $planning->getStart()->format('d-m-Y'), $planning->getEnd()->format('d-m-Y'))) {
                        return new Response(json_encode(array('planExist' => true, $plan)));
                    }
                }
            }
            $temps = $form->get('temps')->getData();
            if (in_array('Matin', $temps) && in_array('Après-midi', $temps)) {
                $planning->setAllDay(true);
            } elseif (in_array('Matin', $temps) && !in_array('Après-midi', $temps)) {
                $planning->setAllDay(false);
                $planning->setStart($form->get('start')->getData());
                $planning->getStart()->setTime(8, 30);
                $planning->setEnd($form->get('end')->getData());
                $planning->getEnd()->setTime(13, 00);
            } elseif (!in_array('Matin', $temps) && in_array('Après-midi', $temps)) {
                $planning->setAllDay(false);
                $planning->setStart($form->get('start')->getData());
                $planning->getStart()->setTime(14, 00);
                $planning->setEnd($form->get('end')->getData());
                $planning->getEnd()->setTime(18, 00);
            }
            $em->persist($planning);
            $em->flush();
            return new Response(json_encode(array('status' => 'success')));
        }
        return $this->render('@TimSoftGeneral/Planning/newPlanning.html.twig', array(
            'planning' => $planning,
            'form' => $form->createView(),
        ));
    }

}
