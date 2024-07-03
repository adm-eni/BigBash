<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CampusController extends AbstractController
{
    #[Route('/campus', name: 'app_campus')]
    public function index(): Response
    {
        return $this->render('campus/campus.index.html.twig', [
            'controller_name' => 'CampusController',
        ]);
    }
}
