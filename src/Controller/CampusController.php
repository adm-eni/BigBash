<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\CampusType;
use App\Repository\CampusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/campuses', name: 'campus_')]
class CampusController extends AbstractController
{
  #[Route('/', name: 'list')]
  public function list(
      CampusRepository $repo
  ): Response
  {
    $campuses = $repo->findAll();

    $campus = new Campus();
    $form = $this->createForm(CampusType::class, $campus);

    return $this->render('campus/campus.index.html.twig', [
        'campuses' => $campuses,
        'form' => $form
    ]);
  }

}
