<?php

namespace TimSoft\GeneralBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use TimSoft\GeneralBundle\Entity\Affaire;
use TimSoft\GeneralBundle\Entity\Client;

/**
 * Affaire controller.
 *
 * @Route("affaire")
 */
class AffaireController extends Controller
{
    /**
     * Lists all affaire entities.
     *
     * @Route("/", name="affaire_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $affaires = $em->getRepository('TimSoftGeneralBundle:Affaire')->findAll();

        return $this->render('@TimSoftGeneral/affaire/index.html.twig', array(
            'affaires' => $affaires,
        ));
    }


    /**
     * Creates a new affaire entity.
     *
     * @Route("/new", name="affaire_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $affaire = new Affaire();
        $form = $this->createForm('TimSoft\GeneralBundle\Form\AffaireType', $affaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($affaire);
            $em->flush();

            return $this->redirectToRoute('affaire_index', array('id' => $affaire->getId()));
        }

        return $this->render('@TimSoftGeneral/affaire/new.html.twig', array(
            'affaire' => $affaire,
            'form' => $form->createView(),
        ));
    }


    /**
     * Finds and displays a affaire entity.
     *
     * @Route("/{id}", name="affaire_show")
     * @Method("GET")
     */
    public function showAction(Affaire $affaire)
    {
        $deleteForm = $this->createDeleteForm($affaire);

        return $this->render('@TimSoftGeneral/affaire/show.html.twig', array(
            'affaire' => $affaire,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing affaire entity.
     *
     * @Route("/{id}/edit", name="affaire_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Affaire $affaire)
    {
        $deleteForm = $this->createDeleteForm($affaire);
        $editForm = $this->createForm('TimSoft\GeneralBundle\Form\AffaireType', $affaire);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('affaire_edit', array('id' => $affaire->getId()));
        }

        return $this->render('@TimSoftGeneral/affaire/edit.html.twig', array(
            'affaire' => $affaire,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }


    /**
     * Deletes a affaire entity.
     *
     * @Route("/{id}", name="affaire_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Affaire $affaire)
    {
        $form = $this->createDeleteForm($affaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($affaire);
            $em->flush();
        }

        return $this->redirectToRoute('affaire_index');
    }

    /**
     * Creates a form to delete a affaire entity.
     *
     * @param Affaire $affaire The affaire entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Affaire $affaire)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('affaire_delete', array('id' => $affaire->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @Route("/client/{client}", name="affaire_by_client", options = { "expose" = true })
     * @Method({"GET", "POST"})
     * @param Client $client
     * @return JsonResponse
     */
    public function ayhaja(Client $client)
    {
        $em = $this->getDoctrine()->getManager();

        $affaires = $em->getRepository('TimSoftGeneralBundle:Affaire')->findByClient($client);

        return new JsonResponse($affaires);
    }

}
