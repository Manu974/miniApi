<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;
use AppBundle\Entity\User;
use AppBundle\Entity\Task;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use AppBundle\Exception\ResourceValidationException;
use Nelmio\ApiDocBundle\Annotation as Doc;


class ApiController extends FOSRestController
{
    /**
     * @Rest\Get(
     *     path = "/api/users/{id}",
     *     name = "app_user_show",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View(
     *              statusCode = 200
     *              )
     * @Doc\ApiDoc(
     *     section="Users",
     *     resource=true,
     *     description="Get one user.",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirements"="\d+",
     *             "description"="The user unique identifier."
     *         }
     *      
     *     },
     *       statusCodes={
     *         200="Returned when succesful",
     *         400="Returned when a violation is raised by validation"
     *       }
     * )
     */
    public function showUserAction(User $user)
    {
        

        return array($user);

    }

    /**
     * @Rest\Get(
     *     path = "/api/tasks/{id}",
     *     name = "app_task_show",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View(
     *              statusCode = 200
     *              )
     * @Doc\ApiDoc(
     *    section="Tasks",
     *     resource=true,
     *     description="Get one task.",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirements"="\d+",
     *             "description"="The task unique identifier."
     *         }
     *     },
     *       statusCodes={
     *         200="Returned when succesful",
     *         400="Returned when a violation is raised by validation"
     *       }
     * )
     */
    public function showTaskAction(Task $task)
    {

        return $task;

    }

     /**
     * @Rest\Post(
     *    path = "/api/tasks/{user_id}",
     *    name = "app_task_create",
     *     requirements = {"user_id"="\d+"}
     * )
     * @Rest\View(StatusCode = 201)
     * @ParamConverter("task", converter="fos_rest.request_body")
     * @Doc\ApiDoc(
     *    section="Tasks",
     *     resource=true,
     *     description="Create a task.",
     *     requirements={
     *         {
     *             "name"="user_id",
     *             "dataType"="integer",
     *             "requirements"="\d+",
     *             "description"="The user unique identifier who created the task."
     *         }
     *     },
     *       statusCodes={
     *         201="Returned when created",
     *         400="Returned when a violation is raised by validation"
     *       }
     * )
     */
    public function createTaskAction(Task $task, $user_id, ConstraintViolationList $violations)
    {       

        if (count($violations)) {
            $message = 'The JSON sent contains invalid data. Here are the errors you need to correct: ';
            foreach ($violations as $violation) {
                $message .= sprintf("Field %s: %s ", $violation->getPropertyPath(), $violation->getMessage());
            }

            throw new ResourceValidationException($message);
        }


        $em = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($user_id);
        $task->setUserId($user);
        $task->setCreationDate( new \Datetime('now', new \DateTimeZone('Europe/Paris')));
        $em->persist($task);
        $em->flush();

        

        return $this->view($task, Response::HTTP_CREATED, ['Location' => $this->generateUrl('app_task_show', ['id' => $task->getId(), UrlGeneratorInterface::ABSOLUTE_URL])
            ]);
       
                
    }


    /**
     * @Rest\Post(
     *    path = "/api/users",
     *    name = "app_user_create"
     * )
     * @Rest\View(StatusCode = 201)
     * @ParamConverter("user", converter="fos_rest.request_body")
     * @Doc\ApiDoc(
     *     section="Users",
     *     resource=true,
     *     description="Create a user.",
    *       statusCodes={
     *         201="Returned when created",
     *         400="Returned when a violation is raised by validation"
     *       }
     *     
     * )
     */
    public function createUserAction(User $user, ConstraintViolationList $violations)
    {
        if (count($violations)) {
            $message = 'The JSON sent contains invalid data. Here are the errors you need to correct: ';
            foreach ($violations as $violation) {
                $message .= sprintf("Field %s: %s ", $violation->getPropertyPath(), $violation->getMessage());
            }

            throw new ResourceValidationException($message);
        }


        $em = $this->getDoctrine()->getManager();

        $em->persist($user);
        $em->flush();

       

        return $this->view($user, Response::HTTP_CREATED, ['Location' => $this->generateUrl('app_user_show', ['id' => $user->getId(), UrlGeneratorInterface::ABSOLUTE_URL])
            ]);


                
    }


    /**
     * @Rest\Get(
     *     path = "/api/lists/tasks/{user_id}",
     *     name = "app_list_tasks_show",
     *     requirements = {"user_id"="\d+"}
     * 
     * )
     * @Rest\View(
     *              statusCode = 200
     *              )
     * @Doc\ApiDoc(
     *    section="Tasks",
     *     resource=true,
     *     description="list All task fot current user.",
     *     requirements={
     *         {
     *             "name"="user_id",
     *             "dataType"="integer",
     *             "requirements"="\d+",
     *             "description"="The user unique identifier who created tasks for list."
     *         }
     *     },
    *       statusCodes={
     *         200="Returned when succesful",
     *         400="Returned when a violation is raised by validation"
     *       }
     * )
     */
    public function showListTaskAction($user_id)
    {
            $listTasks = $this->getDoctrine()->getRepository('AppBundle:Task')->findBy(array('user_id' => $user_id));
            
           
            return $listTasks;

    }

    /**
     * @Rest\Get(
     *     path = "/api/lists/users",
     *     name = "app_list_users_show",
     * 
     * )
     * @Rest\View(
     *              statusCode = 200
     *              )
     * @Doc\ApiDoc(
     *     section="Users",
     *     resource=true,
     *     description="list All users.",
     *       statusCodes={
     *         200="Returned when succesful",
     *         400="Returned when a violation is raised by validation"
     *       }
     * )
     *     
     * )
     */
    public function showListUserAction()
    {
            $listUsers = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();
            
            return $listUsers;

    }

    /**
     * @Rest\View(StatusCode = 204)
     * @Rest\Delete(
     *     path = "/api/users/{id}",
     *     name = "app_user_delete",
     *     requirements = {"id"="\d+"}
     * )
     * @Doc\ApiDoc(
     *     section="Users",
     *     resource=true,
     *     description="delete a user",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirements"="\d+",
     *             "description"="The user unique identifier"
     *         }
     *     },
     *       statusCodes={
     *         200="Returned when succesful",
     *         400="Returned when a violation is raised by validation"
     *       }
     * )
     * )
     */
    public function deleteUserAction(User $user)
    {
        $em = $this->getDoctrine()->getManager();

        $tasks = $this->getDoctrine()->getRepository('AppBundle:Task')->findBy(array('user_id' => $user->getId()));

        foreach ($tasks as $task) {
            $em->remove($task);
        }

        $em->remove($user);
        $em->flush();

        
    }


    /**
     * @Rest\View(StatusCode = 204)
     * @Rest\Delete(
     *     path = "/api/tasks/{id}",
     *     name = "app_task_delete",
     *     requirements = {"id"="\d+"}
     * )
     * @Doc\ApiDoc(
     *    section="Tasks",
     *     resource=true,
     *     description="delete a task",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirements"="\d+",
     *             "description"="The task unique identifier"
     *         }
     *     },
     *       statusCodes={
     *         200="Returned when succesful",
     *         400="Returned when a violation is raised by validation"
     *       }
     * )
     * )
     */
    public function deleteTaskAction(Task $task)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($task);
        $em->flush();

        
    }



    
}
