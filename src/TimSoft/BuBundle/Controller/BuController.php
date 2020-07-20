<?php

namespace TimSoft\BuBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use TimSoft\BuBundle\Entity\Bu;

/**
 * Bu controller.
 *
 */
class BuController extends Controller
{
    /**
     * Lists all bu entities.
     *
     */
    public function indexAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager();

        $bus = $em->getRepository('TimSoftBuBundle:Bu')->findAll();

        return $this->render('bu/index.html.twig', array(
            'bus' => $bus,
        ));
    }

    /**
     * Creates a new bu entity.
     *
     */
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $bu = new Bu();
        $form = $this->createForm('TimSoft\BuBundle\Form\BuType', $bu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            foreach ($bu->getUtilisateurs() as $utilisateur) {
                $utilisateur->addBu($bu);
                $em->persist($utilisateur);
            }
          //  var_dump($bu->getUtilisateurs());
            $em->persist($bu);
            $em->flush();

            return $this->redirectToRoute('bu_index');
        }

        return $this->render('bu/new.html.twig', array(
            'bu' => $bu,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a bu entity.
     *
     */
    public function showAction(Bu $bu)
    {
        $deleteForm = $this->createDeleteForm($bu);

        return $this->render('bu/show.html.twig', array(
            'bu' => $bu,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing bu entity.
     *
     */
    public function editAction(Request $request, Bu $bu)
    {
        $deleteForm = $this->createDeleteForm($bu);
        $editForm = $this->createForm('TimSoft\BuBundle\Form\BuType', $bu);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('bu_edit', array('id' => $bu->getId()));
        }

        return $this->render('bu/edit.html.twig', array(
            'bu' => $bu,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a bu entity.
     *
     */
    public function deleteAction($id)
    {

        $bu = $this->getDoctrine()->getRepository('TimSoftBuBundle:Bu')->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($bu);
        $em->flush();

        return $this->redirectToRoute('bu_index');
    }

    /**
     * Creates a form to delete a bu entity.
     *
     * @param Bu $bu The bu entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Bu $bu)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('bu_delete', array('id' => $bu->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
