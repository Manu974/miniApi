<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;
use AppBundle\Entity\User;
use AppBundle\Entity\Task;
use FOS\RestBundle\Controller\Annotations as Rest;


class ApiController extends Controller
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

    
}
