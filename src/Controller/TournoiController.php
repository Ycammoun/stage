<?php

namespace App\Controller;

use App\Entity\Partie;
use App\Entity\Poule;
use App\Entity\Tableau;
use App\Entity\Tournoi;
use App\Form\CalculTournoiType;
use App\Service\CalculTournoiService;
use App\Service\CreateMatche;
use App\Service\Distribution;
use App\Service\EquipeCsvImporter;
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
    #[Route('/tournoi/poule/{id}/delete', name: '_deletepoule')]
    public function deletePoule(int $id, EntityManagerInterface $em, Request $request): Response
    {
        $poule = $em->getRepository(Poule::class)->find($id);

        if (!$poule) {
            throw $this->createNotFoundException('Poule non trouvée');
        }

        $tableau = $poule->getTableau();
        $tournoi = $tableau->getTournoi();

        // Supprimer la poule
        $em->remove($poule);
        $em->flush();

        // Redirection vers la gestion du tournoi
        return $this->redirectToRoute('formgestiontournoi', [
            'tournoiId' => $tournoi->getId()
        ]);
    }
    #[Route('/tableaudelete/{id}', name: '_deletetableau')]
    public function deleteTab(EntityManagerInterface $em, int $id): Response
    {
        $tableau = $em->getRepository(Tableau::class)->find($id);

        if (!$tableau) {
            // Affiche un message d'erreur si le tableau n'existe pas
            throw $this->createNotFoundException("Le tableau avec l'ID $id n'existe pas.");
        }

        $tournoi = $tableau->getTournoi();

        // Optionnel : supprimer les poules associées manuellement si nécessaire
        foreach ($tableau->getPoules() as $poule) {
            $em->remove($poule);
        }

        $em->remove($tableau);
        $em->flush();

        return $this->redirectToRoute('formgestiontournoi', [
            'tournoiId' => $tournoi->getId(),
        ]);
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
    #[Route('/distribution2', name: '_distribution2')]
    public function indexx(): Response
    {
        return $this->render('tournoi/index.html.twig');
    }






    #[Route('/distribution/json', name: 'distribution_json')]
    public function showDistributionJson(Distribution $distribution): JsonResponse
    {
        $data = $distribution->getRepartitions();
        return $this->json($data);
    }
    #[Route('/creatematches', name: '_createMatches')]
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
        $gagnant=$win->win($poule->getId());
        dump($gagnant);
        return $this->render('tournoi/gagnant.html.twig', [
            'gagnant' => $gagnant,
            'poule'=>$poule
        ]);
    }
    #[Route('/affichepoules/{id}', name: '_affichepoules')]
    public function affichePoulesAction(int $id,EntityManagerInterface $em):Response{
        $tournoi = $em->getRepository(Tournoi::class)->find($id);
        $tabs = $tournoi->getTableaux();

        $poules = [];

        foreach ($tabs as $tab) {
            foreach ($tab->getPoules() as $poule) {
                // ✅ forcer le chargement des parties
                $poule->getParties()->toArray();
                $poules[] = $poule;
            }
        }

        return $this->render('tournoi/affichePoules.html.twig', [
            'poules' => $poules,
            'tournoi' => $tournoi,
        ]);
    }
    #[Route('/import-equipes', name: 'import_equipes')]
    public function importEquipes(EquipeCsvImporter $importer): Response
    {
        $csvPath = $this->getParameter('kernel.project_dir') . '/public/fichier.csv';

        try {
            $importer->import($csvPath);
            return new Response('Import terminé avec succès.');
        } catch (\Exception $e) {
            return new Response('Erreur lors de l\'import : ' . $e->getMessage(), 500);
        }
    }







}
