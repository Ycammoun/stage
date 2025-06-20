<?php

namespace App\Controller;

use App\Entity\Equipe;
use App\Entity\Partie;
use App\Entity\Poule;
use App\Form\setScoreForm;
use App\Service\MatchDistributionService;
use App\Service\setScore;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/utilisateur', name: 'utilisateur')]
final class UtilisateurController extends AbstractController
{
    #[Route('/affichematches', name: '_affichematches')]
    public function affichematches(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $equipes = $entityManager->getRepository(Equipe::class)->findByUser($user);

        if (empty($equipes)) {
            return $this->render('tournoi/affichematches.html.twig', [
                'matches' => [],
                'message' => 'Aucune équipe trouvée pour cet utilisateur.'
            ]);
        }

        $matches = [];

        foreach ($equipes as $equipe) {
            foreach ($equipe->getMatchs1() as $match) {
                $matches[] = $match;
            }


            foreach ($equipe->getMatchs2() as $match) {
                $matches[] = $match;
            }
        }

        // Supprimer les doublons en comparant les ids des matchs
        $uniqueMatches = [];
        foreach ($matches as $match) {
            $uniqueMatches[$match->getId()] = $match;
        }

        // Les valeurs du tableau $uniqueMatches contiennent les matchs sans doublon
        $matches = array_values($uniqueMatches);

        return $this->render('tournoi/affichematches.html.twig', [
            'matches' => $matches,
        ]);
    }
    #[Route('/api/matches', name: 'api_affichematches', methods: ['GET'])]
    public function apiAffichematches(
        EntityManagerInterface $entityManager,
        Security $security
    ): JsonResponse {
        $user = $security->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non authentifié'], 401);
        }

        $equipes = $entityManager->getRepository(Equipe::class)->findByUser($user);

        if (empty($equipes)) {
            return new JsonResponse([
                'matches' => [],
                'message' => 'Aucune équipe trouvée pour cet utilisateur.',
                'utilisateur' => [
                    'prenom' => $user->getPrenom(),
                    'nom' => $user->getNom(),
                ]
            ]);
        }

        $matches = [];

        foreach ($equipes as $equipe) {
            foreach ($equipe->getMatchs1() as $match) {
                $matches[$match->getId()] = $match;
            }
            foreach ($equipe->getMatchs2() as $match) {
                $matches[$match->getId()] = $match;
            }
        }

        $matchesArray = array_map(function ($match) {
            return [
                'id' => $match->getId(),
                'equipe1' => $match->getEquipe1()->getNom(),
                'equipe2' => $match->getEquipe2()->getNom(),
                'scoreEquipe1' => $match->getScore1(),
                'scoreEquipe2' => $match->getScore2(),
                'date' => $match->getDate() ? $match->getDate()->format('Y-m-d') : null,
                'terrain' => $match->getTerrain() ? $match->getTerrain()->getNumero() : 'Inconnu',

            ];
        }, array_values($matches));

        return new JsonResponse([
            'matches' => $matchesArray,
            'utilisateur' => [
                'prenom' => $user->getPrenom(),
                'nom' => $user->getNom(),
            ]
        ]);
    }



    #[Route('/setScore/{idMatche}', name: '_setScore')]
    public function setScore(EntityManagerInterface $entityManager,Request $request,int $idMatche,MatchDistributionService $redistribut): Response
    {
        $matche=$entityManager->getRepository(Partie::class)->find($idMatche);

        $form=$this->createForm(setScoreForm::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data=$form->getData();

            $setScore = new setScore($entityManager);
            $setScore->setScore(
                $data['score1'],
                $data['score2'],
                $matche,

            );
            $user = $this->getUser();

            if ($matche->getEquipe1()->getJoueurs()->contains($user)) {
                $matche->setSetParEquipe(1);
            } elseif ($matche->getEquipe2()->getJoueurs()->contains($user)) {
                $matche->setSetParEquipe(2);
            }

            $terrain=$matche->getTerrain();

            $matche->setTerrain(NULL);
            $matche->setEnCours(false); // <-- ligne manquante ici !

            if ($terrain !== null) {
                $terrain->setEstOccupé(false);
                $entityManager->persist($terrain);
            }
            $entityManager->persist($matche);
            $entityManager->flush();
            $redistribut->redistribution($matche->getPoule()->getTableau()->getTournoi());

            return $this->redirectToRoute('utilisateur_affichematches');
        }


        return $this->render('tournoi/setScore.html.twig', ['matche' => $matche, 'form' => $form->createView(), 'message' => '']);
    }
    #[Route('/api/setScore/{idMatche}', name: 'api_set_score', methods: ['POST'])]
    public function setScoreApi(
        EntityManagerInterface $entityManager,
        Request $request,
        int $idMatche,
        MatchDistributionService $redistribut
    ): JsonResponse
    {
        $matche = $entityManager->getRepository(Partie::class)->find($idMatche);

        if (!$matche) {
            return new JsonResponse(['error' => 'Match non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data['score1'], $data['score2'])) {
            return new JsonResponse(['error' => 'Les scores sont requis'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $setScore = new setScore($entityManager);
            $setScore->setScore($data['score1'], $data['score2'], $matche);

            $terrain = $matche->getTerrain();

            $matche->setTerrain(null);
            $matche->setEnCours(false);

            if ($terrain !== null) {
                $terrain->setEstOccupé(false);
                $entityManager->persist($terrain);
            }
            $user = $this->getUser();
            if ($matche->getEquipe1()->getJoueurs()->contains($user)) {
                $matche->setSetParEquipe(1);
            } elseif ($matche->getEquipe2()->getJoueurs()->contains($user)) {
                $matche->setSetParEquipe(2);
            }

            $entityManager->persist($matche);
            $entityManager->flush();

            $redistribut->redistribution($matche->getPoule()->getTableau()->getTournoi());

            return new JsonResponse(['message' => 'Score enregistré avec succès'], Response::HTTP_OK);

        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur serveur: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    #[Route('/validerScore/{idMatche}', name: '_valider_score')]
    public function validerScore(EntityManagerInterface $entityManager, int $idMatche): Response
    {
        $user = $this->getUser();
        $matche = $entityManager->getRepository(Partie::class)->find($idMatche);

        if (!$matche) {
            throw $this->createNotFoundException("Match introuvable.");
        }



        $matche->setIsValideParAdversaire(true);
        $entityManager->flush();

        return $this->redirectToRoute('utilisateur_affichematches');
    }
    #[Route('/api/validerScore/{idMatche}', name: 'api_valider_score', methods: ['POST'])]
    public function validerScoreApi(EntityManagerInterface $entityManager, int $idMatche): JsonResponse
    {
        $user = $this->getUser();
        $matche = $entityManager->getRepository(Partie::class)->find($idMatche);

        if (!$matche) {
            return $this->json([
                'success' => false,
                'message' => 'Match introuvable.'
            ], 404);
        }

        // Ici tu peux ajouter une vérification d'autorisation si nécessaire

        $matche->setIsValideParAdversaire(true);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Score validé avec succès.',
            'idMatche' => $idMatche,
            //'valideParAdversaire' => $matche->getIsValideParAdversaire(),
        ]);
    }

    #[Route('/api/poules' , name: '_api_poules')]
    public function affichePouleAction(EntityManagerInterface $em): JsonResponse
    {
        try {
            $poules = $em->getRepository(Poule::class)->findAll();

            $poulesArray = [];

            foreach ($poules as $poule) {
                $equipesArray = [];
                foreach ($poule->getEquipes() as $equipe) {
                    $equipesArray[] = [
                        'id' => $equipe->getId(),
                        'nom' => $equipe->getNom(),
                    ];
                }

                $partiesArray = [];
                foreach ($poule->getParties() as $partie) {
                    $partiesArray[] = [
                        'id' => $partie->getId(),
                        'equipe1' => $partie->getEquipe1()?->getNom(),
                        'equipe2' => $partie->getEquipe2()?->getNom(),
                        'score1' => $partie->getScore1(),
                        'score2' => $partie->getScore2(),
                        'date' => $partie->getDate()?->format('Y-m-d H:i'),
                    ];
                }

                $poulesArray[] = [
                    'id' => $poule->getId(),
                    'numero' => $poule->getNumero(),
                    'equipes' => $equipesArray,
                    'parties' => $partiesArray,
                ];
            }

            return new JsonResponse(['poules' => $poulesArray]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

}
