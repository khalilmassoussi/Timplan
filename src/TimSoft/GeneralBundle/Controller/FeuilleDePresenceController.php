<?php

namespace TimSoft\GeneralBundle\Controller;

use TimSoft\GeneralBundle\Entity\FeuilleDePresence;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Feuilledepresence controller.
 *
 * @Route("feuilledepresence")
 */
class FeuilleDePresenceController extends Controller
{
    /**
     * Lists all feuilleDePresence entities.
     *
     * @Route("/", name="feuilledepresence_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $feuilleDePresences = $em->getRepository('TimSoftGeneralBundle:FeuilleDePresence')->findAll();

        return $this->render('feuilledepresence/index.html.twig', array(
            'feuilleDePresences' => $feuilleDePresences,
        ));
    }

    /**
     * Finds and displays a feuilleDePresence entity.
     *
     * @Route("/{id}", name="feuilledepresence_show")
     * @Method("GET")
     */
    public function showAction(FeuilleDePresence $feuilleDePresence)
    {

        return $this->render('feuilledepresence/show.html.twig', array(
            'feuilleDePresence' => $feuilleDePresence,
        ));
    }
}
