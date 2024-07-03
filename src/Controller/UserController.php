<?php

namespace App\Controller;

use App\Entity\Outing;
use App\Entity\User;
use App\Exception\OutingStatusException;
use App\Form\UserProfileType;
use App\Repository\UserRepository;
use App\Service\OutingService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/user', name: 'user_')]
class UserController extends AbstractController
{
    public function __construct(private UserService $userService, private OutingService $outingService)
    {
    }

    #[Route('/profile', name: 'profile')]
    public function profile(Request                     $request,
                            EntityManagerInterface      $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(UserProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('cancel')->isClicked()) {
                return $this->redirectToRoute('outing_list');
            }

            $this->userService->updateUserProfile($user, $form);
            $entityManager->flush();

            $this->addFlash('success', 'Profil mis à jour !');
            return $this->redirectToRoute('user_profile');
        }

        return $this->render('user/user.profile.html.twig', [
            'profileForm' => $form,
            'user' => $user,
        ]);
    }

    #[Route('/{id}', name: 'show', requirements: ['id' => '\d+'])]
    public function show(Request        $request,
                         UserRepository $userRepository,
                         int            $id = null): Response
    {
        if ($id === null) {
            return $this->redirectToRoute('user_profile');
        }

        $user = $userRepository->find($id);

        if ($user === null) {
            $this->addFlash('error','Utilisateur non trouvé.');
            return $this->redirectToRoute('outing_list');
        }

        return $this->render('user/user.show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/join/{outingId}', name: 'join', requirements: ['id' => '\d+'])]
    public function join(EntityManagerInterface $entityManager,
                         Outing                 $outingId = null): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$outingId) {
            $this->addFlash('error','Sortie non trouvée. Inscription non effectuée.');
            return $this->redirectToRoute('outing_list');
        }

        if ($outingId->getHost() === $user) {
            $this->addFlash('error', 'Vous êtes déjà inscrit à une sortie dont vous êtes l\'organisateur.');
            return $this->redirectToRoute('outing_list');
        }

        try {
            $this->outingService->checkOutingStatus($outingId, true, true, true, true, false, true);
        } catch (OutingStatusException $e) {
            $this->addFlash($e->getFlashType(), $e->getMessage());
            return $this->redirectToRoute('outing_list');
        }

        $user->addEnteredOuting($outingId);
        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'Inscription réussie');
        return $this->redirectToRoute('outing_list');
    }

    #[Route('/withdraw/{outingId}', name: 'withdraw', requirements: ['id' => '\d+'])]
    public function withdraw(EntityManagerInterface $entityManager,
                             Outing                 $outingId = null): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$outingId) {
            $this->addFlash('error', 'Sortie non trouvée. Désistement non effectué.');
            return $this->redirectToRoute('outing_list');
        }

        if ($outingId->getHost() === $user) {
            $this->addFlash('error', 'Impossible de se désister d\'une sortie dont vous êtes l\'organisateur.');
            return $this->redirectToRoute('outing_list');
        }

        try {
            $this->outingService->checkOutingStatus($outingId, true, true, true, true, false, true);
        } catch (OutingStatusException $e) {
            $this->addFlash($e->getFlashType(), $e->getMessage());
            return $this->redirectToRoute('outing_list');
        }

        $user->removeEnteredOuting($outingId);
        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'Désistement réussie');
        return $this->redirectToRoute('outing_list');
    }
}