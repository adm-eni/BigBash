<?php

namespace App\Controller;

use App\Repository\CampusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/campuses', name: 'campus_')]
class CampusController extends AbstractController
{
  #[Route('/', name: 'list')]
  public function index(
    CampusRepository $repo
  ): Response
  {
    $campuses = $repo->findAll();
    return $this->render('campus/campus.index.html.twig', [
        'campuses' => $campuses
    ]);
  }
}
