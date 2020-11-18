<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TounamentController extends AbstractController
{
    /**
     * @Route("/api/tounament/test", name="test")
     */
    public function index(): Response
    {

    }
}
