<?php

namespace App\Controller;

use App\Entity\Outing;
use App\Entity\User;
use App\Enum\Status;
use App\Form\UserProfileType;
use App\Repository\UserRepository;
use App\Utils\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/user', name: 'user_')]
class UserController extends AbstractController
{
    #[Route('/profile', name: 'profile')]
    public function profile(Request                     $request,
                            UserPasswordHasherInterface $userPasswordHasher,
                            EntityManagerInterface      $entityManager,
                            FileUploader                $fileUploader): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(UserProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('cancel')->isClicked()) {
                return $this->redirectToRoute('outing_list');
            }

            if (!empty($form->get('plainPassword')->getData())) {
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
            }

            if (!empty($form->get('image')->getData())) {
                $file = $form->get('image')->getData();
                $newFilename = $fileUploader->upload($file, $this->getParameter('profile_image_directory'), $user->getPseudo());
                $user->setImage($newFilename);
            }

            $entityManager->persist($user);
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
            throw $this->createNotFoundException('Page non trouvée.');
        } else {
            $user = $userRepository->find($id);
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
            throw $this->createNotFoundException('Sortie non trouvée. Inscription non effectuée.');
        }

        if($outingId->getStatus() === Status::CLOSED) {
            throw $this->createNotFoundException('Incription impossible. Sortie terminée.');
        }
        if($outingId->getStatus() === Status::CANCELED) {
            throw $this->createNotFoundException('Incription impossible. Sortie annulée.');
        }
        if($outingId->getStatus() === Status::CREATED) {
            throw $this->createNotFoundException('Incription impossible.');
        }
        if($outingId->getStatus() === Status::PAST) {
            throw $this->createNotFoundException('Incription impossible. Sortie passée.');
        }
        if($outingId->getStatus() === Status::ONGOING) {
            throw $this->createNotFoundException('Incription impossible. Sortie déjà débutée!');
        }

        if($outingId->getHost() === $user) {
            throw $this->createAccessDeniedException('Vous êtes déjà inscrit à une sortie dont vous êtes l\'organisateur.');
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
            throw $this->createNotFoundException('Sortie non trouvée. Désistement non effectué.');
        }

        if($outingId->getStatus() === Status::CLOSED) {
            throw $this->createNotFoundException('Désistement impossible. Sortie terminée.');
        }
        if($outingId->getStatus() === Status::CANCELED) {
            throw $this->createNotFoundException('Désistement impossible. Sortie annulée.');
        }
        if($outingId->getStatus() === Status::CREATED) {
            throw $this->createNotFoundException('Désistement impossible.');
        }
        if($outingId->getStatus() === Status::PAST) {
            throw $this->createNotFoundException('Désistement impossible. Sortie passée.');
        }
        if($outingId->getStatus() === Status::ONGOING) {
            throw $this->createNotFoundException('Désistement impossible. Sortie déjà débutée!');
        }

        if($outingId->getHost() === $user) {
            throw $this->createAccessDeniedException('Impossible de se désister d\'une sortie dont vous êtes l\'organisateur.');
        }

        $user->removeEnteredOuting($outingId);
        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'Désistement réussie');
        return $this->redirectToRoute('outing_list');
    }
}
