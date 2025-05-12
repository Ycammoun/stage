<?php

namespace App\Controller;

use App\Entity\Tableau;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
#[Route(path:'/tableau', name: 'tableau')]

final class TableauController extends AbstractController
{
    #[Route('/tableau', name: 'app_tableau')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/TableauController.php',
        ]);
    }
    #[Route(path: '/liste/{id}', name: '_liste')]
    public function listeAction(EntityManagerInterface $entityManager,int $id){
        $Tableau=$entityManager->getRepository(Tableau::class)->find($id);
        $args=array(
            'tab'=>$Tableau,
        );

        return $this->render('tournoi/listeTab.html.twig', $args);
    }
}
