<?php

namespace App\Controller;

use App\Entity\Equipe;
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
use Twig\Environment;

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

    #[Route('/apresmidi/{id}', name: 'afficher_apres_midi', methods: ['GET'])]
    public function afficherMatchsApresMidi(
        int $id,
        EntityManagerInterface $entityManager
    ): Response {
        $poule = $entityManager->getRepository(Poule::class)->find($id);

        if (!$poule) {
            throw $this->createNotFoundException('Poule non trouvée');
        }

        // Récupérer les matchs de l'après-midi uniquement (bracket = true)
        $matchsApresMidi = $entityManager->getRepository(Partie::class)->findBy([
            'poule' => $poule,
            'bracket' => true,
        ]);

        $m1 = $matchsApresMidi[0] ?? null;
        $m2 = $matchsApresMidi[1] ?? null;
        $m3 = $matchsApresMidi[2] ?? null;
        $m4 = $matchsApresMidi[3] ?? null;
        $m5 = $matchsApresMidi[4] ?? null;

        // Création dynamique du match 3 si conditions remplies
        if ($m1 && $m1->isValideParAdversaire() && !$m3) {
            $m3 = new Partie();
            $m3->setEquipe1($poule->getEquipes()[0] ?? null); // Adapte selon ta logique
            $m3->setEquipe2($this->getGagnant($m1));
            $m3->setBracket(true);
            $m3->setPoule($poule);
            $m3->setEnCours(false);
            $m3->setScore1(0);
            $m3->setScore2(0);
            $m3->setIsValideParAdversaire(false);

            $entityManager->persist($m3);
            $entityManager->flush();

            // Recharger les matchs
            $matchsApresMidi = $entityManager->getRepository(Partie::class)->findBy([
                'poule' => $poule,
                'bracket' => true,
            ]);
            $m3 = $matchsApresMidi[2] ?? $m3; // au cas où l'ordre change
        }

        // Création dynamique du match 4 si m2 et m3 validés, et m4 n'existe pas
        if ($m2 && $m3 && $m2->isValideParAdversaire() && $m3->isValideParAdversaire() && !$m4) {
            $m4 = new Partie();
            $m4->setEquipe1($this->getGagnant($m2));
            $m4->setEquipe2($this->getGagnant($m3));
            $m4->setBracket(true);
            $m4->setPoule($poule);
            $m4->setEnCours(false);
            $m4->setScore1(0);
            $m4->setScore2(0);
            $m4->setIsValideParAdversaire(false);

            $m5=new Partie();
            $m5->setEquipe1($this->getPerdant($m2));
            $m5->setEquipe2($this->getPerdant($m3));
            $m5->setBracket(true);
            $m5->setPoule($poule);
            $m5->setEnCours(false);
            $m5->setScore1(0);
            $m5->setScore2(0);
            $m5->setIsValideParAdversaire(false);

            $entityManager->persist($m5);
            $entityManager->persist($m4);
            $entityManager->flush();

            // Recharger les matchs
            $matchsApresMidi = $entityManager->getRepository(Partie::class)->findBy([
                'poule' => $poule,
                'bracket' => true,
            ]);
            $m4 = $matchsApresMidi[3] ?? $m4;
        }

        return $this->render('tournoi/gagnant.html.twig', [
            'poule' => $poule,
            'm1' => $matchsApresMidi[0] ?? null,
            'm2' => $matchsApresMidi[1] ?? null,
            'm3' => $matchsApresMidi[2] ?? null,
            'm4' => $matchsApresMidi[3] ?? null,
            'm5' => $matchsApresMidi[4] ?? null,
        ]);
    }

    #[Route('/gagnant/{id}', name: 'gagnant')]
    public function generateTournament(Win $win, EntityManagerInterface $entityManager, int $id): Response
    {
        $poule = $entityManager->getRepository(Poule::class)->find($id);
        if (!$poule) {
            throw $this->createNotFoundException('Poule non trouvée');
        }

        $gagnant = $win->win($poule->getId());
        dump($gagnant);

        $m1 = $m2 = $m3 = $m4 = $m5 = $m6 = $m7 = $m8 = $m11 = null;

        // On attend exactement 5 équipes
        if (count($gagnant) === 5) {
            $m1 = new Partie();
            $m1->setEquipe1($gagnant[1]);
            $m1->setEquipe2($gagnant[2]);
            $m1->setBracket(true);
            $m1->setPoule($poule);
            $m1->setEnCours(false);
            $m1->setScore1(0); // valeurs fictives
            $m1->setScore2(0);
            $m1->setIsValideParAdversaire(false);

            $m2 = new Partie();
            $m2->setEquipe1($gagnant[3]);
            $m2->setEquipe2($gagnant[4]);
            $m2->setBracket(true);

            $m2->setPoule($poule);
            $m2->setEnCours(false);
            $m2->setScore1(0);
            $m2->setScore2(0);
            $m2->setIsValideParAdversaire(false);

            $entityManager->persist($m1);
            $entityManager->persist($m2);

            if ($m1->isValideParAdversaire()) {
                $m3 = new Partie();
                $m3->setEquipe1($gagnant[0]);
                $m3->setEquipe2($this->getGagnant($m1));
                $m3->setBracket(true);

                $m3->setPoule($poule);
                $m3->setEnCours(false);
                $m3->setScore1(0);
                $m3->setScore2(0);
                $m3->setIsValideParAdversaire(false);

                $entityManager->persist($m3);
            }

            if ($m3 && $m3->isValideParAdversaire()) {
                $m4 = new Partie();
                $m4->setEquipe1($this->getGagnant($m3));
                $m4->setEquipe2($this->getGagnant($m2));
                $m4->setBracket(true);

                $m4->setPoule($poule);
                $m4->setEnCours(false);
                $m4->setScore1(0);
                $m4->setScore2(0);
                $m4->setIsValideParAdversaire(false);

                $entityManager->persist($m4);
            }

            $entityManager->flush();
        }
        else if(count($gagnant) === 6){
            $m1 = new Partie();
            $m1->setEquipe1($gagnant[3]);
            $m1->setEquipe2($gagnant[4]);
            $m1->setPoule($poule);
            $m1->setEnCours(false);
            $m1->setScore1(10);
            $m1->setScore2(20);
            $m1->setIsValideParAdversaire(true);
            $entityManager->persist($m1);
            $m2= new Partie();
            $m2->setEquipe1($gagnant[2]);
            $m2->setEquipe2($gagnant[5]);
            $m2->setPoule($poule);
            $m2->setEnCours(false);
            $m2->setScore1(11);
            $m2->setScore2(22);
            $m2->setIsValideParAdversaire(true);
            $entityManager->persist($m2);

            if ($m1->isValideParAdversaire()) {
                $m3= new Partie();
                $m3->setEquipe1($gagnant[0]);
                $m3->setEquipe2($this->getGagnant($m1));
                $m3->setPoule($poule);
                $m3->setEnCours(false);
                $m3->setScore1(3);
                $m3->setScore2(9);
                $m3->setIsValideParAdversaire(true);
                $entityManager->persist($m3);
            }
            if($m2->isValideParAdversaire()){
                $m4= new Partie();
                $m4->setEquipe1($gagnant[1]);
                $m4->setEquipe2($this->getGagnant($m2));
                $m4->setPoule($poule);
                $m4->setEnCours(false);
                $m4->setScore1(40);
                $m4->setScore2(30);
                $m4->setIsValideParAdversaire(true);
                $entityManager->persist($m4);
            }
            if ($m3->isValideParAdversaire() && $m4->isValideParAdversaire()) {
                $m5 = new Partie();
                $m5->setEquipe1($this->getGagnant($m3));
                $m5->setEquipe2($this->getGagnant($m4));
                $m5->setPoule($poule);
                $m5->setEnCours(false);
                $m5->setScore1(23);
                $m5->setScore2(18);
                $m5->setIsValideParAdversaire(true);
                $entityManager->persist($m5);
                $entityManager->flush();



            }

            return $this->render('tournoi/gagnant6.html.twig', [
                'gagnant' => $gagnant,
                'poule' => $poule,
                'm1' => $m1,
                'm2' => $m2,
                'm3' => $m3,
                'm4' => $m4,
                'm5' => $m5,
            ]);

        }
        else if (count($gagnant)===7){
            $m1 = new Partie();
            $m1->setEquipe1($gagnant[3]);
            $m1->setEquipe2($gagnant[4]);
            $m1->setPoule($poule);
            $m1->setEnCours(false);
            $m1->setScore1(13);
            $m1->setScore2(22);
            $m1->setIsValideParAdversaire(true);
            $entityManager->persist($m1);
            $m2= new Partie();
            $m2->setEquipe1($gagnant[2]);
            $m2->setEquipe2($gagnant[5]);
            $m2->setPoule($poule);
            $m2->setEnCours(false);
            $m2->setScore1(12);
            $m2->setScore2(22);
            $m2->setIsValideParAdversaire(true);
            $entityManager->persist($m2);
            $m3= new Partie();
            $m3->setEquipe1($gagnant[1]);
            $m3->setEquipe2($gagnant[6]);
            $m3->setPoule($poule);
            $m3->setEnCours(false);
            $m3->setScore1(12);
            $m3->setScore2(22);
            $m3->setIsValideParAdversaire(true);
            $entityManager->persist($m3);
            if ($m1->isValideParAdversaire()) {
                $m5= new Partie();
                $m5->setEquipe1($gagnant[0]);
                $m5->setEquipe2($this->getGagnant($m1));
                $m5->setPoule($poule);
                $m5->setEnCours(false);
                $m5->setScore1(12);
                $m5->setScore2(22);
                $m5->setIsValideParAdversaire(true);
                $entityManager->persist($m5);

            }
            if($m2->isValideParAdversaire()&&$m3->isValideParAdversaire()){
                $m4= new Partie();
                $m4->setEquipe1($this->getGagnant($m2));
                $m4->setEquipe2($this->getGagnant($m3));
                $m4->setPoule($poule);
                $m4->setEnCours(false);
                $m4->setScore1(12);
                $m4->setScore2(22);
                $m4->setIsValideParAdversaire(true);
                $entityManager->persist($m4);
                $m7=new Partie();
                $m7->setEquipe1($this->getPerdant($m2));
                $m7->setEquipe2($this->getPerdant($m3));
                $m7->setPoule($poule);
                $m7->setEnCours(false);
                $m7->setScore1(12);
                $m7->setScore2(22);
                $m7->setIsValideParAdversaire(true);
                $entityManager->persist($m4);
            }
            if ($m5->isValideParAdversaire()&&$m4->isValideParAdversaire()) {
                $m11= new Partie();
                $m11->setEquipe1($this->getGagnant($m4));
                $m11->setEquipe2($this->getGagnant($m5));
                $m11->setPoule($poule);
                $m11->setEnCours(false);
                $m11->setScore1(12);
                $m11->setScore2(22);
                $m11->setIsValideParAdversaire(true);
                $entityManager->persist($m11);
                $m6= new Partie();
                $m6->setEquipe1($this->getPerdant($m4));
                $m6->setEquipe2($this->getPerdant($m5));
                $m6->setPoule($poule);
                $m6->setEnCours(false);
                $m6->setScore1(12);
                $m6->setScore2(22);
                $m6->setIsValideParAdversaire(true);
                $entityManager->persist($m6);
            }
            if ($m1->isValideParAdversaire() && $m7->isValideParAdversaire()) {
                $m8= new Partie();
                $m8->setEquipe1($this->getPerdant($m1));
                $m8->setEquipe2($this->getGagnant($m7));
                $m8->setPoule($poule);
                $m8->setEnCours(false);
                $m8->setScore1(12);
                $m8->setScore2(22);
                $m8->setIsValideParAdversaire(true);
                $entityManager->persist($m8);

            }
            return $this->render('tournoi/gagnant7.html.twig', [
                'gagnant' => $gagnant,
                'poule' => $poule,
                'm1' => $m1,
                'm2' => $m2,
                'm3' => $m3,
                'm4' => $m4,
                'm5' => $m5,
                'm6' => $m6,
                'm7' => $m7,
                'm8' => $m8,
                'm11' => $m11 ?? null,
            ]);
        }


        return $this->render('tournoi/gagnant.html.twig', [
            'gagnant' => $gagnant,
            'poule' => $poule,
            'm1' => $m1,
            'm2' => $m2,
            'm3' => $m3,
            'm4' => $m4,
        ]);
    }

// Méthode ajoutée
    private function getGagnant(Partie $partie): Equipe
    {
        return $partie->getScore1() > $partie->getScore2()
            ? $partie->getEquipe1()
            : $partie->getEquipe2();
    }
    private function getPerdant(Partie $partie): Equipe
    {
        return $partie->getScore1() < $partie->getScore2()
            ? $partie->getEquipe1()
            : $partie->getEquipe2();
    }

    #[Route('/affichepoules/{id}', name: '_affichepoules')]
    public function affichePoulesAction(int $id,EntityManagerInterface $em):Response{
        $tournoi = $em->getRepository(Tournoi::class)->find($id);
        $tabs = $tournoi->getTableaux();

        $poules = [];

        foreach ($tabs as $tab) {
            foreach ($tab->getPoules() as $poule) {
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
