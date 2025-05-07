<?php

namespace App\Controller;

use App\Entity\Tournoi;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/tournoi', name: 'tournoi')]
final class TournoiController extends AbstractController
{
    #[Route('/tournoi', name: 'app_tournoi')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/TournoiController.php',
        ]);
    }
    #[Route('/list', name: '_list')]
    public function listTournoi(EntityManagerInterface $em):Response
    {
        $dernierTournoi = $em->getRepository(Tournoi::class)->findOneBy([], ['id' => 'DESC']);
        dump($dernierTournoi);
        return $this->render('tournoi/list.html.twig', [
            'tournoi' => $dernierTournoi,
        ]);
    }
}
