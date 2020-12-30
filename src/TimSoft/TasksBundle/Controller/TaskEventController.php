<?php

namespace TimSoft\TasksBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TimSoft\TasksBundle\Entity\TaskEvent;

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
     * @param Request $request
     * @return JsonResponse
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $taskEvents = $em->getRepository('TimSoftTasksBundle:TaskEvent')->betweenDates($request->get('start'), $request->get('end'), $this->getUser());

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
            $taskExist = false;
            $date = [];
            $taskEvents = $em->getRepository('TimSoftTasksBundle:TaskEvent')->findByUtilisateur($taskEvent->getUtilisateur());
            if (!$taskEvent->getPeriodique()) {
                foreach ($taskEvents as $task) {
                    if (($task->isAllDay() && $taskEvent->isAllDay()) || (!$task->isAllDay() && !$taskEvent->isAllDay())) {
                        if ($this->getIntersection($task->getStart(), $task->getEnd(), $taskEvent->getStart(), $taskEvent->getEnd())) {
                            return new Response(json_encode(array('taskExist' => true, $taskEvents, 'a55')));
                        }
                    } else {
                        if ($this->getIntersection(strtotime($task->getStart()->format('d-m-Y')), strtotime($task->getEnd()->format('d-m-Y')), strtotime($taskEvent->getStart()->format('d-m-Y')), strtotime($taskEvent->getEnd()->format('d-m-Y')))) {
                            return new Response(json_encode(array('taskExist' => true, $taskEvent->getStart()->format('d-m-Y'), $task->getStart()->format('d-m-Y'))));
                        }
                    }
                }
            }

            if (!$taskEvent->getByweekday()) {
                $taskEvent->setByweekday(null);
            }
            /*----------------------------*/
            $previous_week = strtotime("-1 week +1 day");

            $start_week = strtotime("last sunday midnight", $previous_week);
            $end_week = strtotime("next saturday", $start_week);

            $start_week = date("Y-m-d", $start_week);
            $end_week = date("Y-m-d", $end_week);
            /*---------------------------*/

            if ($taskEvent->getStart()->format('Y-m-d') < $end_week) {
                return new Response(json_encode("ErreurPlanificationEnArriere"), 419);
            }

            $em->persist($taskEvent);
            $em->flush();
            return new Response(json_encode(array('status' => 'success', $taskEvent, $date)));
        }

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
     * @param Request $request
     * @param TaskEvent $taskEvent
     * @return Response
     */
    public function editAction(Request $request, TaskEvent $taskEvent)
    {
        $deleteForm = $this->createDeleteForm($taskEvent);
        $editForm = $this->createForm('TimSoft\TasksBundle\Form\TaskEventType', $taskEvent);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $taskEvents = $em->getRepository('TimSoftTasksBundle:TaskEvent')->findByUtilisateur($taskEvent->getUtilisateur());
            $taskEvents = $this->unsetValue($taskEvents, $taskEvent, true);
            if (!$taskEvent->getByweekday()) {
                $taskEvent->setByweekday(null);
            }
            if (!$taskEvent->getPeriodique()) {
                foreach ($taskEvents as $task) {
                    if (($task->isAllDay() && $taskEvent->isAllDay()) || (!$task->isAllDay() && !$taskEvent->isAllDay())) {
                        if ($this->getIntersection($task->getStart(), $task->getEnd(), $taskEvent->getStart(), $taskEvent->getEnd())) {
                            return new Response(json_encode(array('taskExist' => true, $task)));
                        }
                    } else {
                        if ($this->getIntersection(strtotime($task->getStart()->format('d-m-Y')), strtotime($task->getEnd()->format('d-m-Y')), strtotime($taskEvent->getStart()->format('d-m-Y')), strtotime($taskEvent->getEnd()->format('d-m-Y')))) {
                            return new Response(json_encode(array('taskExist' => true, $task)));
                        }
                    }
                }
            }
            /*----------------------------*/
            $previous_week = strtotime("-1 week +1 day");

            $start_week = strtotime("last sunday midnight", $previous_week);
            $end_week = strtotime("next saturday", $start_week);

            $start_week = date("Y-m-d", $start_week);
            $end_week = date("Y-m-d", $end_week);
            /*---------------------------*/

            if ($taskEvent->getStart()->format('Y-m-d') < $end_week) {
                return new Response(json_encode("ErreurPlanificationEnArriere"), 419);
            }
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
     * @Route("/{id}/delete", name="taskevent_delete")
     * @param $id
     * @return Response
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $taskEvent = $em->getRepository('TimSoftTasksBundle:TaskEvent')->find($id);
        $em->remove($taskEvent);
        $em->flush();
//        }
        return new Response(json_encode(array('status' => 'success')));

//        return $this->redirectToRoute('taskevent_index');
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
    public function taskByClientAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $taskEvents = $em->getRepository('TimSoftTasksBundle:TaskEvent')->findByClient($id, $request->get('start'), $request->get('end'), $this->getUser());

        return new JsonResponse($taskEvents);
    }

    /**
     * @param $id
     * @return JsonResponse
     * @Route("/get-tasks-by-user/{id}", name="taskevent_by_user",  options = { "expose" = true })
     * @Method("GET")
     */
    public function taskByUserAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $taskEvents = $em->getRepository('TimSoftTasksBundle:TaskEvent')->findByUser($id, $request->get('start'), $request->get('end'));

        return new JsonResponse($taskEvents);
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

    function unsetValue(array $array, $value, $strict = TRUE)
    {
        if (($key = array_search($value, $array, $strict)) !== FALSE) {
            unset($array[$key]);
        }
        return $array;
    }
}
