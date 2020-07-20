<?php

namespace TimSoft\TasksBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use TimSoft\TasksBundle\Entity\TaskEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Taskevent controller.
 *
 * @Route("taskevent")
 */
class TaskEventController extends Controller
{
    /**
     * Lists all taskEvent entities.
     *
     * @Route("/", name="taskevent_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $taskEvents = $em->getRepository('TimSoftTasksBundle:TaskEvent')->findAll();

        return new JsonResponse($taskEvents);
    }

    /**
     * Creates a new taskEvent entity.
     *
     * @Route("/new", name="taskevent_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $taskEvent = new Taskevent();
        $form = $this->createForm('TimSoft\TasksBundle\Form\TaskEventType', $taskEvent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $plannings = $em->getRepository('TimSoftGeneralBundle:Planning')->findByUser($taskEvent->getUtilisateur());
            $PlanningExist = null;
            foreach ($plannings as $planning) {
                if (($planning->isAllDay() && $taskEvent->isAllDay()) || ($planning->isAllDay() && !$taskEvent->isAllDay()) || (!$planning->isAllDay() && $taskEvent->isAllDay())) {
                    if ($planning->getStart()->format('Y-m-d') <= $taskEvent->getStart()->format('Y-m-d') && $planning->getEnd()->format('Y-m-d') >= $taskEvent->getEnd()->format('Y-m-d')) {
                        $PlanningExist = true;
                    }
                } else {
                    if ($planning->getStart()->format('Y-m-d H:i') <= $taskEvent->getStart()->format('Y-m-d H:i') && $planning->getEnd()->format('Y-m-d H:i') >= $taskEvent->getEnd()->format('Y-m-d H:i')) {
                        $PlanningExist = true;
                    }
                }
//                print_r(json_encode($taskEvent->getStart()->format('Y-m-d')));
//                echo '<pre>';
//                print_r(json_encode($planning->getStart()->format('Y-m-d')));
            }
//            print_r(json_encode($plannings));
//            die();
            $em->persist($taskEvent);
            $em->flush();
            if ($PlanningExist) {
                return new Response(json_encode(array('PlanningExist' => $PlanningExist)));
            }
            return new Response(json_encode(array('status' => 'success')));
//            $referer = $request->headers->get('referer');
//            return $this->redirect($referer);
//            return $this->redirectToRoute('taskevent_show', array('id' => $taskEvent->getId()));
        }
//        if ($form->isSubmitted() && !$form->isValid()) {
//            return new Response(json_encode(array('status' => 'errrrrrrrr')));
//        }

        return $this->render('@TimSoftTasks/taskevent/newForm.html.twig', array(
            'taskEvent' => $taskEvent,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a taskEvent entity.
     *
     * @Route("/{id}", name="taskevent_show", options = { "expose" = true })
     * @Method("GET")
     */
    public function showAction(TaskEvent $taskEvent)
    {

        return $this->render('@TimSoftTasks/taskevent/showForm.html.twig', array(
            'taskEvent' => $taskEvent,
//            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing taskEvent entity.
     *
     * @Route("/{id}/edit", name="taskevent_edit", options = { "expose" = true })
     * @Method({"GET", "POST"})
     *
     */
    public function editAction(Request $request, TaskEvent $taskEvent)
    {
        $deleteForm = $this->createDeleteForm($taskEvent);
        $editForm = $this->createForm('TimSoft\TasksBundle\Form\TaskEventType', $taskEvent);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return new Response(json_encode(array('status' => 'success')));
        }

        return $this->render('@TimSoftTasks/taskevent/editForm.html.twig', array(
            'taskEvent' => $taskEvent,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a taskEvent entity.
     *
     * @Route("/{id}", name="taskevent_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, TaskEvent $taskEvent)
    {
        $form = $this->createDeleteForm($taskEvent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($taskEvent);
            $em->flush();
        }

        return $this->redirectToRoute('taskevent_index');
    }

    /**
     * Creates a form to delete a taskEvent entity.
     *
     * @param TaskEvent $taskEvent The taskEvent entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(TaskEvent $taskEvent)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('taskevent_delete', array('id' => $taskEvent->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/get-tasks-from-activite", name="taskevent_list_tasks")
     * @Method("GET")
     */
    public function listTasksOfActiviteAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $tasksRepo = $em->getRepository("TimSoftTasksBundle:Task");

        $tasks = $tasksRepo->createQueryBuilder("q")
            ->where("q.activite = :activiteId")
            ->setParameter("activiteId", $request->query->get("activiteId"))
            ->getQuery()
            ->getResult();

        $responseArray = array();
        foreach ($tasks as $task) {
            $responseArray[] = array(
                "id" => $task->getId(),
                "libelle" => $task->getLibelle()
            );
        }
        return new JsonResponse($responseArray);
    }

    /**
     * @param $id
     * @return JsonResponse
     * @Route("/get-tasks-by-client/{id}", name="taskevent_by_client",  options = { "expose" = true })
     * @Method("GET")
     */
    public function taskByClientAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $taskEvents = $em->getRepository('TimSoftTasksBundle:TaskEvent')->findByClient($id);

        return new JsonResponse($taskEvents);
    }

    /**
     * @param $id
     * @return JsonResponse
     * @Route("/get-tasks-by-user/{id}", name="taskevent_by_user",  options = { "expose" = true })
     * @Method("GET")
     */
    public function taskByUserAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $taskEvents = $em->getRepository('TimSoftTasksBundle:TaskEvent')->findByUtilisateur($id);

        return new JsonResponse($taskEvents);
    }
}
