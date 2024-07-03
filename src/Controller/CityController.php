<?php

namespace App\Controller;

use App\Repository\CityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cities', name: 'city_')]
class CityController extends AbstractController
{
  #[Route('/', name: 'list')]
  public function list(
      CityRepository $repo
  ): Response
  {
    $cities = $repo->findAll();;
    return $this->render('city/city.index.html.twig', [
        'cities' => $cities,
    ]);
  }
}
