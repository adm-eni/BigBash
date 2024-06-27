<?php

namespace App\Controller;

use App\Form\Model\OutingsFilter;
use App\Form\OutingsFilterType;
use App\Repository\OutingRepository;
use App\Service\FilterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('', name: 'outing_')]
class OutingController extends AbstractController
{
  #[Route('', name: 'list')]
  public function list(
      Request          $request,
      OutingRepository $repo,
      FilterService    $service
  ): Response
  {

    $filters = new OutingsFilter();
    $filterForm = $this->createForm(OutingsFilterType::class, $filters);
    $filterForm->handleRequest($request);

    if ($filterForm->isSubmitted() && $filterForm->isValid()) {
      $outings = $service->filterOutings($filters, $this->getUser());
    } else {
      $outings = $repo->findAll();
    }

    return $this->render('outing/outing.index.html.twig', [
        'outings' => $outings,
        'filter_form' => $filterForm
    ]);
  }

  #[Route('/outings/{id}', name: 'outing', requirements: ['id' => '\d+'])]
  public function outing(
      int              $id,
      OutingRepository $outingRepo
  ): Response
  {
    $outing = $outingRepo->find($id);

    return $this->render('outing/outing.show.html.twig', [
        'outing' => $outing
    ]);


  }
}
