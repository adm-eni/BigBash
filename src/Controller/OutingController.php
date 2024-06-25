<?php

namespace App\Controller;

use App\Repository\OutingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('', name: 'outing_')]
class OutingController extends AbstractController
{
  #[Route('', name: 'list')]
  public function list(
      OutingRepository $outingRepo
  ): Response
  {

//    $outings = $outingRepo->findAll();

    return $this->render('outing/outing.index.html.twig',
//        [
//        'outings' => $outings
//    ]
    );
  }
//
//  #[Route('/outings/{id}', name: 'outing', requirements: ['id' => '\d+'])]
//  public function outing(
//      int              $id,
//      OutingRepository $outingRepo
//  ): Response
//  {
//    $outing = $outingRepo->find($id);
//
//    return $this->render('outing/outing.show.html.twig', [
//        'outing' => $outing
//    ]);
//  }
}
