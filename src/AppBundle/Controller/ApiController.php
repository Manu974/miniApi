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







class ApiController extends FOSRestController
{
    /**
     * @Rest\Get(
     *     path = "/users/{id}",
     *     name = "app_user_show",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View(
     *              statusCode = 200
     *              )
     */
    public function showUserAction(User $user)
    {
        

        return array($user);

    }

    /**
     * @Rest\Get(
     *     path = "/tasks/{id}",
     *     name = "app_task_show",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View(
     *              statusCode = 200
     *              )
     */
    public function showTaskAction(Task $task)
    {

        return $task;

    }

     /**
     * @Rest\Post(
     *    path = "/tasks/{user_id}",
     *    name = "app_task_create",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View(StatusCode = 201)
     * @ParamConverter("task", converter="fos_rest.request_body")
     */
    public function createTaskAction(Task $task, $user_id, ConstraintViolationList $violations)
    {       

        if (count($violations)) {
            return $this->view($violations, Response::HTTP_BAD_REQUEST);
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
     *    path = "/users",
     *    name = "app_user_create"
     * )
     * @Rest\View(StatusCode = 201)
     * @ParamConverter("user", converter="fos_rest.request_body")
     */
    public function createUserAction(User $user, ConstraintViolationList $violations)
    {
        if (count($violations)) {
            return $this->view($violations, Response::HTTP_BAD_REQUEST);
        }
        
        $em = $this->getDoctrine()->getManager();

        $em->persist($user);
        $em->flush();

       

        return $this->view($user, Response::HTTP_CREATED, ['Location' => $this->generateUrl('app_user_show', ['id' => $user->getId(), UrlGeneratorInterface::ABSOLUTE_URL])
            ]);


                
    }


    /**
     * @Rest\Get(
     *     path = "/lists/tasks/{user_id}",
     *     name = "app_list_tasks_show",
     *     requirements = {"id"="\d+"}
     * 
     * )
     * @Rest\View(
     *              statusCode = 200
     *              )
     */
    public function showListTaskAction($user_id)
    {
            $listTasks = $this->getDoctrine()->getRepository('AppBundle:Task')->findBy(array('user_id' => $user_id));
            
           
            return $listTasks;

    }

    /**
     * @Rest\Get(
     *     path = "/lists/users",
     *     name = "app_list_user_show",
     * 
     * )
     * @Rest\View(
     *              statusCode = 200
     *              )
     */
    public function showListUserAction()
    {
            $listUsers = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();
            
            return $listUsers;

    }

    /**
     * @Rest\View(StatusCode = 204)
     * @Rest\Delete(
     *     path = "/users/{id}",
     *     name = "app_user_delete",
     *     requirements = {"id"="\d+"}
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
     *     path = "/tasks/{id}",
     *     name = "app_task_delete",
     *     requirements = {"id"="\d+"}
     * )
     */
    public function deleteTaskAction(Task $task)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($task);
        $em->flush();

        
    }



    
}
