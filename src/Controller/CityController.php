<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cities', name: 'city_')]
class CityController extends AbstractController
{
#[Route('/', name: 'list')]
  public function list(): Response
  {
    return $this->render('city/city.index.html.twig', [
        'controller_name' => 'CityController',
    ]);
  }
}
