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

        return $user;

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
     *    path = "/tasks",
     *    name = "app_task_create"
     * )
     * @Rest\View(StatusCode = 201)
     * @ParamConverter("task", converter="fos_rest.request_body")
     */
    public function createTaskAction(Task $task)
    {
        $em = $this->getDoctrine()->getManager();

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
    public function createUserAction(User $user)
    {
        $em = $this->getDoctrine()->getManager();

        $em->persist($user);
        $em->flush();

        return $this->view($user, Response::HTTP_CREATED, ['Location' => $this->generateUrl('app_user_show', ['id' => $user->getId(), UrlGeneratorInterface::ABSOLUTE_URL])
            ]);
                
    }


    /**
     * @Rest\Get(
     *     path = "/lists",
     *     name = "app_list_show",
     * 
     * )
     * @Rest\View(
     *              statusCode = 200
     *              )
     */
    public function showListAction()
    {
            $list = $this->getDoctrine()->getRepository('AppBundle:Task')->findAll();
            dump($list); die;
            return $user;

    }



    
}
