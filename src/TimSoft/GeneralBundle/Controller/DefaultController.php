<?php

namespace TimSoft\GeneralBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use TimSoft\GeneralBundle\TokenStore\TokenCache;


class DefaultController extends Controller
{


//    public function sendNotification(Request $request)
//    {
//        $manager = $this->get('mgilet.notification');
//        $notif = $manager->createNotification('Hello world !');
//        $notif->setMessage('This a notification.');
//        $notif->setLink('http://symfony.com/');
//        // or the one-line method :
//        // $manager->createNotification('Notification subject','Some random text','http://google.fr');
//
//        // you can add a notification to a list of entities
//        // the third parameter ``$flush`` allows you to directly flush the entities
//        $manager->addNotification(array($this->getUser()), $notif, true);
//
//        return $this->redirectToRoute('AjouterClient');
//    }

//    public function rubriqueUtilisateurAction() {
//        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
//            throw $this->createAccessDeniedException();
//        }
//        $em = $this->getDoctrine()->getManager(); // initialise la connexion à la BD
//        $Droits = $em->getRepository('TimSoftGeneralBundle:DroitAcces')->getAllDroit();
//        $Rubriques = array();
//        foreach ($Droits as $Droit) {
//            $d1 = $em->getRepository('TimSoftGeneralBundle:DroitAccesPersonne')->getAutorisationPersonne($Droit->getRouteDroit(), $this->getUser());
//            $d2 = $em->getRepository('TimSoftGeneralBundle:DroitAccesGroupe')->getAutorisationGroupe($Droit->getRouteDroit(), $this->getUser()->getRoleUtilisateur());
//            if ($d1 || $d2 )
//            {
//               $Rubriques[] = $Droit->getCategorieDroit();
//            }
//        }
//        $result = array_unique($Rubriques);
//         return array('result'   => json_encode($result),
//        );
////        return new JsonResponse($result);
//    }

    public function redirectionUtilisateurAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $user = $this->getUser();

        if (($user->getRoleUtilisateur() === "ROLE_ADMIN") || ($user->getRoleUtilisateur() === "ROLE_GESTIONNAIRE")) {
            return $this->render('@TimSoftGeneral/General/AccueilAdmin.html.twig');
        } elseif (($user->getRoleUtilisateur() === "ROLE_CLIENT")) {
            return $this->redirectToRoute('ConsulterFeuillesRapportInterventionParClient');
        } elseif ($user->getRoleUtilisateur() === "ROLE_CONSULTANT") {
            if ($this->AutorisationAcces('dashboard', $this->getUser())) {
                return $this->render('@TimSoftGeneral/General/AccueilChef.html.twig');
            }
            return $this->render('@TimSoftCommande/Default/mesplans.html.twig');
        } elseif ($user->getRoleUtilisateur() === "ROLE_CHEF") {
            if ($this->AutorisationAcces('dashboard', $this->getUser())) {
                return $this->render('@TimSoftGeneral/General/AccueilChef.html.twig');
            }
            return $this->render('@TimSoftCommande/Default/mesplans.html.twig');

        } elseif (($user->getRoleUtilisateur() === "ROLE_TRACKING")) {
            if ($this->AutorisationAcces('dashboard', $this->getUser())) {
                return $this->render('@TimSoftGeneral/General/AccueilChef.html.twig');
            }
            return $this->render('@TimSoftCommande/Default/mesplans.html.twig');
        } elseif (($user->getRoleUtilisateur() === "ROLE_EXTERNE")) {
            return $this->render('@TimSoftCommande/Default/mesplans.html.twig');
        } elseif (($user->getRoleUtilisateur() === "ROLE_SUPPORT")) {
            return $this->render('@TimSoftCommande/Default/mesplans.html.twig');
        }
        return new JsonResponse('cc');
    }

    public function dashboardAction()
    {

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        $ca = $this->getDoctrine()->getRepository('TimSoftCommandeBundle:LigneCommande')->caBu();
        // $ca = array($ca);
        return $this->render('@TimSoftGeneral/General/AccueilChef.html.twig');
    }

    public function AutorisationAcces($Route, $Utilisateur)
    {
        $em = $this->getDoctrine()->getManager();
        $AutorisationPersonne = $em->getRepository('TimSoftGeneralBundle:DroitAccesPersonne')->getAutorisationPersonne($Route, $Utilisateur);
        $AutorisationGroupe = $em->getRepository('TimSoftGeneralBundle:DroitAccesGroupe')->getAutorisationGroupe($Route, $Utilisateur->getRoleUtilisateur());
        if ($AutorisationPersonne || $AutorisationGroupe) {
            return true;
        } else {
            return false;
        }
    }

    public function logAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('Gedmo\Loggable\Entity\LogEntry'); // we use default log entry class
        $plannings = $this->getDoctrine()->getRepository('TimSoftGeneralBundle:Planning')->findAll();
        $logs = $repo->findBy(array(), array('loggedAt' => 'DESC'));
        return $this->render('@TimSoftGeneral/General/logs.html.twig', array('logs' => $logs));
    }

    public function authAction()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Initialize the OAuth client
        $oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId' => '8ce5f5d0-4f60-4475-b7c2-8200bd935cd4',
            'clientSecret' => '7]Xyh2FaqP4ek/sy/Cf1ufdaj=3ocH:y',
            'redirectUri' => 'http://localhost:88/PortailTimSoft/web/authorize',
            'urlAuthorize' => 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize',
            'urlAccessToken' => 'https://login.microsoftonline.com/common/oauth2/v2.0/token',
            'urlResourceOwnerDetails' => '',
            'scopes' => 'Calendars.ReadWrite Calendars.ReadWrite.Shared Calendars.Read.Shared openid profile offline_access User.Read Mail.Read'
        ]);

        // Generate the auth URL
        $authorizationUrl = $oauthClient->getAuthorizationUrl();

        // Save client state so we can validate in response
        $_SESSION['oauth_state'] = $oauthClient->getState();

        // Redirect to authorization endpoint
        header('Location: ' . $authorizationUrl);
        //    echo 'ok';
        exit();
    }

    public function outlookAction()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

//         Authorization code should be in the "code" query param
        if (isset($_GET['code'])) {
            // Check that state matches
            if (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth_state'])) {
                exit('State provided in redirect does not match expected value.');
            }

            // Clear saved state
            unset($_SESSION['oauth_state']);

            // Initialize the OAuth client
            $oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
                'clientId' => '8ce5f5d0-4f60-4475-b7c2-8200bd935cd4',
                'clientSecret' => '7]Xyh2FaqP4ek/sy/Cf1ufdaj=3ocH:y',
                'redirectUri' => 'http://localhost:88/PortailTimSoft/web/authorize',
                'urlAuthorize' => 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize',
                'urlAccessToken' => 'https://login.microsoftonline.com/common/oauth2/v2.0/token',
                'urlResourceOwnerDetails' => '',
                'scopes' => 'https://outlook.office.com/mail.read offline_access https://outlook.office.com/tasks.read'

            ]);

            try {
                // Make the token request
                $accessToken = $oauthClient->getAccessToken('authorization_code', [
                    'code' => $_GET['code']
                ]);

                $tokenCache = new TokenCache;
                $tokenCache->storeTokens($accessToken->getToken(), $accessToken->getRefreshToken(),
                    $accessToken->getExpires());

                // Redirect back to mail page
                echo $accessToken;
            } catch (League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
                exit('ERROR getting tokens: ' . $e->getMessage());
            }
            exit();
        } elseif (isset($_GET['error'])) {
            exit('ERROR: ' . $_GET['error'] . ' - ' . $_GET['error_description']);
        }
        //echo 'cc';
//        exit();
    }

    public function oCalendarAction()
    {

        $from_name = "webmaster";
        $from_address = "timplan@timsoft.net";
        $to_name = "Joseph";
        $to_address = "massoussikhalil@gmail.com";
        $startTime = "11/12/2020 18:00:00";
        $endTime = "11/12/2020 19:00:00";
        $subject = "My Test Subject";
        $description = "My Awesome Description";
        $location = "Joe's House";
        $domain = 'exchangecore.com';

        $ical = 'BEGIN:VCALENDAR' . "\r\n" .
            'PRODID:-//Microsoft Corporation//Outlook 10.0 MIMEDIR//EN' . "\r\n" .
            'VERSION:2.0' . "\r\n" .
            'METHOD:REQUEST' . "\r\n" .
            'BEGIN:VTIMEZONE' . "\r\n" .
            'TZID:Eastern Time' . "\r\n" .
            'BEGIN:STANDARD' . "\r\n" .
            'DTSTART:20091101T020000' . "\r\n" .
            'RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=1SU;BYMONTH=11' . "\r\n" .
            'TZOFFSETFROM:-0400' . "\r\n" .
            'TZOFFSETTO:-0500' . "\r\n" .
            'TZNAME:EST' . "\r\n" .
            'END:STANDARD' . "\r\n" .
            'BEGIN:DAYLIGHT' . "\r\n" .
            'DTSTART:20090301T020000' . "\r\n" .
            'RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=2SU;BYMONTH=3' . "\r\n" .
            'TZOFFSETFROM:-0500' . "\r\n" .
            'TZOFFSETTO:-0400' . "\r\n" .
            'TZNAME:EDST' . "\r\n" .
            'END:DAYLIGHT' . "\r\n" .
            'END:VTIMEZONE' . "\r\n" .
            'BEGIN:VEVENT' . "\r\n" .
            'ORGANIZER;CN="' . $from_name . '":MAILTO:' . $from_address . "\r\n" .
            'ATTENDEE;CN="' . $to_name . '";ROLE=REQ-PARTICIPANT;RSVP=TRUE:MAILTO:' . $to_address . "\r\n" .
            'LAST-MODIFIED:' . date("Ymd\TGis") . "\r\n" .
            'UID:' . date("Ymd\TGis", strtotime($startTime)) . rand() . "@" . $domain . "\r\n" .
            'DTSTAMP:' . date("Ymd\TGis") . "\r\n" .
            'DTSTART;TZID="Eastern Time":' . date("Ymd\THis", strtotime($startTime)) . "\r\n" .
            'DTEND;TZID="Eastern Time":' . date("Ymd\THis", strtotime($endTime)) . "\r\n" .
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
//creation of the file on the server
        // $icfFile = $fs->dumpFile($tmpFolder . $fileName, $icsContent);

//message to include as body to your mail
        $body = 'Hello...';
        $user = $this->getUser();
        $messageObject = \Swift_Message::newInstance();
        $messageObject->setSubject("Your meeting has been booked")
            ->setFrom(['timplan@timsoft.net' => "Administrateur TimSoft"])
            ->addTo('k.massoussi@timsoft.com.tn', $user->getNomUtilisateur() . ' ' . $user->getPrenomUtilisateur())
            ->setBody($body)
            // ->setContentType('text/calendar')
            ->attach($attachment);
        $this->get('mailer')->send($messageObject);
        $failedRecipients = [];
        // $numSent = 0;
        $numSent = $this->get('mailer')->send($messageObject, $failedRecipients);
//remove the created file
        // $fs->remove(array('file', $tmpFolder, $fileName));
        print_r($numSent);
        die();
    }

//   function sendIcalEvent($from_name, $from_address, $to_name, $to_address, $startTime, $endTime, $subject, $description, $location)
//    {
//        $domain = 'exchangecore.com';
//
//        //Create Email Headers
//        $mime_boundary = "----Meeting Booking----" . MD5(TIME());
//
//        $headers = "From: " . $from_name . " <" . $from_address . ">\n";
//        $headers .= "Reply-To: " . $from_name . " <" . $from_address . ">\n";
//        $headers .= "MIME-Version: 1.0\n";
//        $headers .= "Content-Type: multipart/alternative; boundary=\"$mime_boundary\"\n";
//        $headers .= "Content-class: urn:content-classes:calendarmessage\n";
//
//        //Create Email Body (HTML)
//        $message = "--$mime_boundary\r\n";
//        $message .= "Content-Type: text/html; charset=UTF-8\n";
//        $message .= "Content-Transfer-Encoding: 8bit\n\n";
//        $message .= "<html>\n";
//        $message .= "<body>\n";
//        $message .= '<p>Dear ' . $to_name . ',</p>';
//        $message .= '<p>' . $description . '</p>';
//        $message .= "</body>\n";
//        $message .= "</html>\n";
//        $message .= "--$mime_boundary\r\n";
//
//        $ical = 'BEGIN:VCALENDAR' . "\r\n" .
//            'PRODID:-//Microsoft Corporation//Outlook 10.0 MIMEDIR//EN' . "\r\n" .
//            'VERSION:2.0' . "\r\n" .
//            'METHOD:REQUEST' . "\r\n" .
//            'BEGIN:VTIMEZONE' . "\r\n" .
//            'TZID:Eastern Time' . "\r\n" .
//            'BEGIN:STANDARD' . "\r\n" .
//            'DTSTART:20091101T020000' . "\r\n" .
//            'RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=1SU;BYMONTH=11' . "\r\n" .
//            'TZOFFSETFROM:-0400' . "\r\n" .
//            'TZOFFSETTO:-0500' . "\r\n" .
//            'TZNAME:EST' . "\r\n" .
//            'END:STANDARD' . "\r\n" .
//            'BEGIN:DAYLIGHT' . "\r\n" .
//            'DTSTART:20090301T020000' . "\r\n" .
//            'RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=2SU;BYMONTH=3' . "\r\n" .
//            'TZOFFSETFROM:-0500' . "\r\n" .
//            'TZOFFSETTO:-0400' . "\r\n" .
//            'TZNAME:EDST' . "\r\n" .
//            'END:DAYLIGHT' . "\r\n" .
//            'END:VTIMEZONE' . "\r\n" .
//            'BEGIN:VEVENT' . "\r\n" .
//            'ORGANIZER;CN="' . $from_name . '":MAILTO:' . $from_address . "\r\n" .
//            'ATTENDEE;CN="' . $to_name . '";ROLE=REQ-PARTICIPANT;RSVP=TRUE:MAILTO:' . $to_address . "\r\n" .
//            'LAST-MODIFIED:' . date("Ymd\TGis") . "\r\n" .
//            'UID:' . date("Ymd\TGis", strtotime($startTime)) . rand() . "@" . $domain . "\r\n" .
//            'DTSTAMP:' . date("Ymd\TGis") . "\r\n" .
//            'DTSTART;TZID="Eastern Time":' . date("Ymd\THis", strtotime($startTime)) . "\r\n" .
//            'DTEND;TZID="Eastern Time":' . date("Ymd\THis", strtotime($endTime)) . "\r\n" .
//            'TRANSP:OPAQUE' . "\r\n" .
//            'SEQUENCE:1' . "\r\n" .
//            'SUMMARY:' . $subject . "\r\n" .
//            'LOCATION:' . $location . "\r\n" .
//            'CLASS:PUBLIC' . "\r\n" .
//            'PRIORITY:5' . "\r\n" .
//            'BEGIN:VALARM' . "\r\n" .
//            'TRIGGER:-PT15M' . "\r\n" .
//            'ACTION:DISPLAY' . "\r\n" .
//            'DESCRIPTION:Reminder' . "\r\n" .
//            'END:VALARM' . "\r\n" .
//            'END:VEVENT' . "\r\n" .
//            'END:VCALENDAR' . "\r\n";
//        $message .= 'Content-Type: text/calendar;name="meeting.ics";method=REQUEST' . "\n";
//        $message .= "Content-Transfer-Encoding: 8bit\n\n";
//        $message .= $ical;
//
//        $mailsent = mail($to_address, $subject, $message, $headers);
//
//        return ($mailsent) ? (true) : (false);
//    }
}
