<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends AbstractController
{
    #[Route('/main', name: 'app_main')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/MainController.php',
        ]);
    }
    #[Route('/', name: 'app_main1')]
    public function index1(): Response
    {
        return $this->render('main.html.twig');
    }
    #[Route('/acceuil', name:'acceuil')]
    public function acceuil(): Response
    {
        return $this->render('acceuil.html.twig');
    }
}

