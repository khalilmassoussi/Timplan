<?php

namespace TimSoft\TasksBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use TimSoft\TasksBundle\Entity\Activite;
use TimSoft\TasksBundle\Form\ActiviteType;

/**
 * Activite controller.
 *
 * @Route("activite")
 */
class ActiviteController extends Controller
{
    /**
     * Lists all activite entities.
     *
     * @Route("/", name="activite_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager();

        $activites = $em->getRepository('TimSoftTasksBundle:Activite')->findAll();

        return $this->render('@TimSoftTasks/activite/index.html.twig', array(
            'activites' => $activites,
        ));
    }

    /**
     * Creates a new activite entity.
     *
     * @Route("/new", name="activite_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $activite = new Activite();
        $form = $this->createForm('TimSoft\TasksBundle\Form\ActiviteType', $activite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($activite);
            $em->flush();

            return $this->redirectToRoute('activite_index');
        }

        return $this->render('@TimSoftTasks/activite/new.html.twig', array(
            'activite' => $activite,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a activite entity.
     *
     * @Route("/{id}", name="activite_show")
     * @Method("GET")
     */
    public function showAction(Activite $activite)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $deleteForm = $this->createDeleteForm($activite);

        return $this->render('@TimSoftTasks/activite/show.html.twig', array(
            'activite' => $activite,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing activite entity.
     * @Route("/{id}/edit", name="activite_edit")
     * @param Request $request
     * @param Activite $activite
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction($id, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        if (null === $activite = $entityManager->getRepository(Activite::class)->find($id)) {
            throw $this->createNotFoundException('No task found for id ' . $id);
        }

        $originalTasks = new ArrayCollection();

        // Create an ArrayCollection of the current Tag objects in the database
        foreach ($activite->getTasks() as $task) {
            $originalTasks->add($task);
        }

        $editForm = $this->createForm(ActiviteType::class, $activite);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            // remove the relationship between the tag and the Task
            foreach ($originalTasks as $task) {
                if (false === $activite->getTasks()->contains($task)) {
                    $entityManager->remove($task);
                }
            }
            $entityManager->persist($activite);
            $entityManager->flush();

            // redirect back to some edit page
            return $this->redirectToRoute('activite_index');
        }

        // render some form template

        return $this->render('@TimSoftTasks/activite/edit.html.twig', array(
            'activite' => $activite,
            'edit_form' => $editForm->createView(),
//            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a activite entity.
     * @Route("/{id}/delete", name="activite_delete", options={"expose"=true})
     * @param $id
     *
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $activite = $em->getRepository('TimSoftTasksBundle:Activite')->find($id);
        $em->remove($activite);
        $em->flush();
//        return new JsonResponse('Success');
        return $this->redirectToRoute('activite_index');
    }

    /**
     * Creates a form to delete a activite entity.
     *
     * @param Activite $activite The activite entity
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Activite $activite)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('activite_delete', array('id' => $activite->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
