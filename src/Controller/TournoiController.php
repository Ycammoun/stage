<?php

namespace App\Controller;

use App\Entity\Partie;
use App\Entity\Poule;
use App\Entity\Tournoi;
use App\Form\CalculTournoiType;
use App\Service\CalculTournoiService;
use App\Service\CreateMatche;
use App\Service\Distribution;
use App\Service\MatchDistributionService;
use App\Service\Win;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
    #[Route('/deletepoule', name: '_deletepoule')]
    public function deletepouleAction(EntityManagerInterface $em){
        $poules=$em->getRepository(Poule::class)->findAll();
        foreach ($poules as $poule){
            $em->remove($poule);
        }
        $em->flush();
        return $this->redirectToRoute('tournoi_list');
    }
    #[Route('/deletematches', name: '_deletematches')]
    public function deletematchesAction(EntityManagerInterface $em){
        $matches=$em->getRepository(Partie::class)->findAll();
        foreach ($matches as $match){
            $em->remove($match);
        }
        $em->flush();

    }
    #[Route('/distribution', name: 'distribution')]
    public function showDistribution(Distribution $distribution): Response
    {
        $data = $distribution->getRepartitions();

        return $this->render('service/distribution.html.twig', [
            'data' => $data
        ]);
    }
    #[Route('/distribution1', name: '_distribution1')]
    public function distribution(CalculTournoiService $calc, Request $request): Response
    {
        $form = $this->createForm(CalculTournoiType::class);
        $form->handleRequest($request);

        $resultats = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $resultats = $calc->calculTournoi(
                $data['nbJoueurs'],
                $data['nbTableaux'],
                $data['nbTerrains']
            );
        }

        return $this->render('service/distribution1.html.twig', [
            'form' => $form->createView(),
            'resultats' => $resultats,
        ]);
    }





    #[Route('/distribution/json', name: 'distribution_json')]
    public function showDistributionJson(Distribution $distribution): JsonResponse
    {
        $data = $distribution->getRepartitions();
        return $this->json($data);
    }
    #[Route('/creatematches', name: 'createMatches')]
    public function createMatchesAction(
        EntityManagerInterface $em,
        CreateMatche $creatematches,
        MatchDistributionService $distributionMatch
    ): Response {
        $tournoi = $em->getRepository(Tournoi::class)->findOneBy([], ['id' => 'DESC']);

        if (!$tournoi) {
            return new Response('Aucun tournoi trouvé.', 404);
        }

        $creatematches->createMatche($tournoi);
        $distributionMatch->distribution($tournoi);

        return new Response('Les matchs ont été générés et distribués.');
    }
    #[Route('/gagnant/{id}', name: 'gagnant')]
    public function gagnantAction(Win $win, EntityManagerInterface $entityManager,int $id):Response{

        $poule=$entityManager->getRepository(Poule::class)->find($id);
        $gagnant=$win->win($poule);
        dump($gagnant);
        return $this->render('tournoi/gagnant.html.twig', [
            'gagnant' => $gagnant,
            'poule'=>$poule
        ]);
    }







}
