<?php

namespace App\Controller;

use App\Entity\Location;
use App\Form\LocationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/locations', name: 'location_')]
class LocationController extends AbstractController
{
    #[Route('/new', name: 'new')]
    public function new(Request       $request,
                        EntityManagerInterface $entityManager): Response
    {
        $location = new Location();

        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('cancel')->isClicked()) {
                return $this->redirectToRoute('outing_new');
            }

            $entityManager->persist($location);
            $entityManager->flush();

            $this->addFlash('success', 'Lieu créé.');
            return $this->redirectToRoute('outing_new');
        }

        return $this->render('location/location.new.html.twig', [
            'locationForm' => $form,
        ]);
    }
}
