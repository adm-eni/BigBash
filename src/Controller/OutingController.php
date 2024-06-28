<?php

namespace App\Controller;

use App\Entity\Outing;
use App\Form\Model\OutingsFilter;
use App\Form\OutingsFilterType;
use App\Form\OutingType;
use App\Repository\OutingRepository;
use App\Repository\StatusRepository;
use App\Service\OutingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('', name: 'outing_')]
class OutingController extends AbstractController
{
  #[Route('', name: 'list')]
  public function list(
      Request       $request,
      OutingService $service
  ): Response
  {

    $service->updateOutingStatuses();

    $user = $this->getUser();

    $filters = new OutingsFilter();
    $filterForm = $this->createForm(OutingsFilterType::class, $filters);
    $filterForm->handleRequest($request);

    $outings = ($this->isGranted('ROLE_ADMIN') ? $service->getOutings() : $service->getDefaultOutings($user));

    if ($filterForm->isSubmitted() && $filterForm->isValid()) {
      $outings = $service->getFilteredOutings($outings, $user, $filters);
    }

    return $this->render('outing/outing.index.html.twig', [
        'outings' => $outings,
        'filter_form' => $filterForm
    ]);
  }

  #[Route('/outings/{id}', name: 'show', requirements: ['id' => '\d+'])]
  public function show(
      int              $id,
      OutingRepository $outingRepo
  ): Response
  {
    $outing = $outingRepo->find($id);

    return $this->render('outing/outing.show.html.twig', [
        'outing' => $outing
    ]);
  }

  #[Route('/outings/new', name: 'new')]
  #[Route('/outings/edit/{id}', name: 'edit', requirements: ['id' => '\d+'])]
  public function create(
      Request                $request,
      EntityManagerInterface $entityManager,
      StatusRepository       $statusRepository,
      int                    $id = null)
  {
    $user = $this->getUser();
    if ($user == null) {
      return $this->redirectToRoute('app_login');
    }

    if ($id == null) {
      $outing = new Outing();
      $outing->setCampus($user->getCampus());
    } else {
      $outing = $entityManager->find(Outing::class, $id);

      if ($outing->getHost() !== $user) {
        throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette sortie.');
      }
      if ($outing->getStatus()->getName() === 'Clôturé') {
        throw $this->createAccessDeniedException('Cette sortie a été clôturée.');
      }
      if ($outing->getStatus()->getName() === 'En cours') {
        throw $this->createAccessDeniedException('Cette sortie est en cours.');
      }
      if ($outing->getStatus()->getName() === 'Passé') {
        throw $this->createAccessDeniedException('Cette sortie est terminée.');
      }
      if ($outing->getStatus()->getName() === 'Annulé') {
        throw $this->createAccessDeniedException('Cette sortie a été annulée.');
      }
      if ($outing->getStatus()->getName() === 'Ouvert') {
        throw $this->createAccessDeniedException('Cette sortie a été publiée, et ne peut donc plus être modifiée.');
      }
    }
    $outing->setHost($user);

    $form = $this->createForm(OutingType::class, $outing);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      if ($form->get('cancel')->isClicked()) {
        return $this->redirectToRoute('outing_list');
      }
      if ($form->get('save')->isClicked()) {
        $status = $statusRepository->findOneBy(['name' => 'En création']);
        $outing->setStatus($status);

        $entityManager->persist($outing);
        $entityManager->flush();
        $this->addFlash('success', 'Sortie sauvegardée, mais non publiée.');
        return $this->redirectToRoute('outing_list');
      }

      $status = $statusRepository->findOneBy(['name' => 'Ouvert']);

      $entityManager->persist($outing);
      $entityManager->flush();

      $this->addFlash('success', 'Sortie publiée!');
      return $this->redirectToRoute('user_profile');
    }

    return $this->render('outing/outing.edit.html.twig', [
        'outingForm' => $form,
    ]);
  }
}
