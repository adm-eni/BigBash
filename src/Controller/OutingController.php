<?php

namespace App\Controller;

use App\Entity\Outing;
use App\Entity\User;
use App\Enum\Status;
use App\Exception\OutingStatusException;
use App\Form\Model\OutingsFilter;
use App\Form\OutingsFilterType;
use App\Form\OutingType;
use App\Repository\OutingRepository;
use App\Service\OutingService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('', name: 'outing_')]
class OutingController extends AbstractController
{
    /**
     * @param Request $request
     * @param OutingService $service
     * @return Response
     */
    #[Route('', name: 'list')]
    #[Route('/outings')]
    public function list(
        Request       $request,
        OutingService $service
    ): Response
    {

        $service->updateOutingStatuses();

        /** @var User $user */
        $user = $this->getUser();

        $filters = new OutingsFilter();
        $filterForm = $this->createForm(OutingsFilterType::class, $filters);
        $filterForm->handleRequest($request);

        $outings = ($this->isGranted('ROLE_ADMIN') ? $service->getAllOutings() : $service->getDefaultFilteredOutings($user));

        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $outings = $service->getUserFilteredOutings($outings, $user, $filters);
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
        if ($outing === null) {
            $this->addFlash('error', 'Sortie non trouvée.');
            return $this->redirectToRoute('outing_list');
        }

        return $this->render('outing/outing.show.html.twig', [
            'outing' => $outing
        ]);
    }

    #[Route('/outings/new', name: 'new')]
    #[Route('/outings/edit/{id}', name: 'edit', requirements: ['id' => '\d+'])]
    public function create(
        Request                $request,
        EntityManagerInterface $entityManager,
        OutingService          $outingService,
        int                    $id = null): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($user === null) {
            return $this->redirectToRoute('app_login');
        }

        if ($id == null || $outingService->getOuting($id) === null) {
            $outing = new Outing();
            $outing->setCampus($user->getCampus());
        } else {
            $outing = $outingService->getOuting($id);

            if ($outing->getHost() !== $user) {
                $this->addFlash('error', 'Vous n\'avez pas accès à cette sortie.');
                return $this->redirectToRoute('outing_list');
            }

            try {
                $outingService->checkOutingStatus($outing, 1, 1, 1, 1, 1, 0);
            } catch (OutingStatusException $e) {
                $this->addFlash($e->getFlashType(), $e->getMessage());
                return $this->redirectToRoute('outing_list');
            }
        }
        $outing->setHost($user);

        $form = $this->createForm(OutingType::class, $outing, [
            'create' => true,
            'cancelOuting' => false,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('cancel')->isClicked()) {
                return $this->redirectToRoute('outing_list');
            }
            if ($form->get('save')->isClicked()) {
                $status = Status::CREATED;
                $outing->setStatus($status);

                $entityManager->persist($outing);
                $entityManager->flush();
                $this->addFlash('success', 'Sortie sauvegardée, mais non publiée.');
                return $this->redirectToRoute('outing_list');
            }
            if ($form->get('delete')->isClicked()) {
                if ($outing->getStatus() !== Status::CREATED) {
                    $this->addFlash('error', 'Cette sortie ne peut pas être supprimée.');
                    return $this->redirectToRoute('user_profile');
                }
                $outingService->deleteOuting($outing);
                $this->addFlash('success', 'Sortie supprimée.');
                return $this->redirectToRoute('user_profile');

            }

            $status = Status::OPEN;
            $outing->setStatus($status);
            $outing->addAttendee($user);

            $entityManager->persist($outing);
            $entityManager->flush();

            $this->addFlash('success', 'Sortie publiée!');
            return $this->redirectToRoute('user_profile');
        }

        return $this->render('outing/outing.edit.html.twig', [
            'outingForm' => $form,
        ]);
    }

    #[Route('/outings/cancel/{id}', name: 'cancel', requirements: ['id' => '\d+'])]
    public function cancel(
        Request                $request,
        EntityManagerInterface $entityManager,
        OutingService          $outingService,
        int                    $id = null): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($user === null) {
            return $this->redirectToRoute('app_login');
        }
        if ($id === null) {
            $this->addFlash('error', 'Sortie non trouvée.');
            return $this->redirectToRoute('outing_list');
        }

        $outing = $outingService->getOuting($id);
        if($outing === null) {
            $this->addFlash('error', 'Sortie non trouvée.');
            return $this->redirectToRoute('outing_list');
        }

        if ($outing->getHost() !== $user) {
            $this->addFlash('error', 'Vous n\'avez pas accès à cette sortie.');
            return $this->redirectToRoute('outing_list');
        }
        try {
            $outingService->checkOutingStatus($outing, true, true, true, true, false, false);
        } catch (OutingStatusException $e) {
            $this->addFlash($e->getFlashType(), $e->getMessage());
            return $this->redirectToRoute('outing_list');
        }

        if ($outing->getStatus() === Status::CREATED) {
            $outingService->deleteOuting($outing);
            $this->addFlash('success', 'Sortie supprimée.');
            return $this->redirectToRoute('user_profile');
        }

        $form = $this->createForm(OutingType::class, $outing, [
            'create' => false,
            'cancelOuting' => true,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('cancel')->isClicked()) {
                return $this->redirectToRoute('outing_list');
            }

            $status = Status::CANCELED;

            $outing->setStatus($status);
            $outing->setDescription($outing->getDescription() . '- Annulé : ' . $form->get('description')->getData());

            $entityManager->persist($outing);
            $entityManager->flush();

            $this->addFlash('success', 'Sortie annulée.');
            return $this->redirectToRoute('user_profile');
        }

        return $this->render('outing/outing.cancel.html.twig', [
            'outing' => $outing,
            'outingCancelForm' => $form,
        ]);
    }
}
