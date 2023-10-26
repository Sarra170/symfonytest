<?php

namespace App\Controller;
use App\Repository\VolRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// Make sure to use the correct namespace

class VolController extends AbstractController
{
    #[Route('/vol', name: 'app_vol')]
    public function index(): Response
    {
        return $this->render('vol/index.html.twig', [
            'controller_name' => 'VolController',
        ]);
    }
    #[Route('/AfficheVol', name: 'app_AfficheVol')]
    public function Affiche(VolRepository $repository, ManagerRegistry $doctrine)
    {
        $vols = $repository->findAll();

        return $this->render('Vol/AfficheVol.html.twig', [
            'vols' => $vols,

        ]);

    }
}
