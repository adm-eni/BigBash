<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/user', name: 'user_')]
class UserController extends AbstractController
{
    #[Route('/profile', name: 'profile')]
    public function profile(): Response
    {
        return $this->render('user/user.profile.html.twig', []);
    }
}
