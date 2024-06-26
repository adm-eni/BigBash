<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserProfileType;
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
                            EntityManagerInterface      $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(UserProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($form->get('cancel')->isClicked()) {
                return $this->redirectToRoute('outing_list');
            }

            if(!empty($form->get('plainPassword')->getData())){
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
            }

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Profil mis à jour !');
            return $this->redirectToRoute('user_profile');
        }

        return $this->render('user/user.profile.html.twig', [
            'profileForm' => $form,
        ]);
    }
}
