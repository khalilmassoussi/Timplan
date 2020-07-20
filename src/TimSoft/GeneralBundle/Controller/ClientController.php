<?php

namespace TimSoft\GeneralBundle\Controller;

use TimSoft\GeneralBundle\Entity\Client;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Client controller.
 *
 */
class ClientController extends Controller
{
    /**
     * Lists all client entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $clients = $em->getRepository('TimSoftGeneralBundle:Client')->findAll();

        return $this->render('index.html.twig', array(
            'clients' => $clients,
        ));
    }

    /**
     * Creates a new client entity.
     *
     */
    public function newAction(Request $request)
    {
        $client = new Client();
        $form = $this->createForm('TimSoft\GeneralBundle\Form\ClientType', $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($client);
            $em->flush();

            return $this->redirectToRoute('GestionDesClients_show', array('codeClient' => $client->getCodeclient()));
        }

        return $this->render('client/new.html.twig', array(
            'client' => $client,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a client entity.
     *
     */
    public function showAction(Client $client)
    {
        $deleteForm = $this->createDeleteForm($client);

        return $this->render('client/show.html.twig', array(
            'client' => $client,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing client entity.
     *
     */
    public function editAction(Request $request, Client $client)
    {
        $deleteForm = $this->createDeleteForm($client);
        $editForm = $this->createForm('TimSoft\GeneralBundle\Form\ClientType', $client);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('GestionDesClients_edit', array('codeClient' => $client->getCodeclient()));
        }

        return $this->render('client/edit.html.twig', array(
            'client' => $client,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a client entity.
     *
     */
    public function deleteAction(Request $request, Client $client)
    {
        $form = $this->createDeleteForm($client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($client);
            $em->flush();
        }

        return $this->redirectToRoute('GestionDesClients_index');
    }

    /**
     * Creates a form to delete a client entity.
     *
     * @param Client $client The client entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Client $client)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('GestionDesClients_delete', array('codeClient' => $client->getCodeclient())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
