<?php

namespace App\Service;

use App\Entity\Outing;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Utils\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService extends AbstractController
{

    public function __construct(private readonly UserRepository              $userRepository,
                                private readonly UserPasswordHasherInterface $passwordHasher,
                                private readonly FileUploader                $fileUploader)
    {
    }

    public function checkUserIsHost(Outing $outing, User $user, string $message): void
    {
        if ($outing->getHost() !== $user) {
            throw $this->createAccessDeniedException($message);
        }
    }

    public function checkUserIsNotHost(Outing $outing, User $user, string $message): void
    {
        if ($outing->getHost() === $user) {
            throw $this->createAccessDeniedException($message);
        }
    }

    public function updateUserProfile(User $user, FormInterface $form): void
    {
        if (!empty($form->get('plainPassword')->getData())) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $form->get('plainPassword')->getData()));
        }

        if (!empty($form->get('image')->getData())) {
            $file = $form->get('image')->getData();
            $newFilename = $this->fileUploader->upload($file, $this->getParameter('profile_image_directory'), $user->getPseudo());
            $user->setImage($newFilename);
        }

        $this->userRepository->getEntityManager()->persist($user);
    }

}