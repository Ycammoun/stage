<?php

namespace App\Controller;

use App\Entity\Tournoi;
use App\Service\CreateMatche;
use App\Service\Distribution;
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

    #[Route('/delete/{id}', name: '_delete')]
    public function deleteAction(EntityManagerInterface $em, int $id){
        $tournoi = $em->getRepository(Tournoi::class)->find($id);
        $em->remove($tournoi);
        $em->flush();
        return $this->redirectToRoute('tournoi_list');
    }
    #[Route('/distribution', name: 'distribution')]
    public function showDistribution(Distribution $distribution): Response
    {
        $data = $distribution->getRepartitions();

        return $this->render('service/distribution.html.twig', [
            'data' => $data
        ]);
    }
    #[Route('/distribution/json', name: 'distribution_json')]
    public function showDistributionJson(Distribution $distribution): JsonResponse
    {
        $data = $distribution->getRepartitions();
        return $this->json($data);
    }
    public function createMatchesAction(EntityManagerInterface $em):Response{
        $creatematches=new CreateMatche($em);
        $creatematches->createMatche();
        return new Response('done');

    }
}
