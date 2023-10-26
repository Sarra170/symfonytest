<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController
{
    #[Route('/reservation', name: 'app_reservation')]
    public function index(): Response
    {
        return $this->render('reservation/index.html.twig', [
            'controller_name' => 'ReservationController',
        ]);
    }
    #[Route('/reservation/add', name: 'app_AddRes')]

    public function  Ajouter (Request  $request, ManagerRegistry $doctrine)
    {
        $reservation=new Reservation();
        $form =$this->CreateForm(ReservationType::class,$reservation)
            ->add('save', SubmitType::class, ['label' => 'add']);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $em=$doctrine->getManager();
            $em->persist($reservation);
            $em->flush();
            return $this->redirectToRoute('app_reservation');
        }
        return $this->render('reservation/Add.html.twig',[
            'f' => $form->createView()
        ]);

    }
}
