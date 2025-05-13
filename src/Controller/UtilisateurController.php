<?php

namespace App\Controller;

use App\Entity\Equipe;
use App\Entity\Partie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/utilisateur', name: 'utilisateur')]
final class UtilisateurController extends AbstractController
{
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UtilisateurController.php',
        ]);
    }
    #[Route('/affichematches', name: 'affichematches')]
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
            // Ajoute les matchs de l'équipe 1
            foreach ($equipe->getMatchs1() as $match) {
                $matches[] = $match;
            }

            // Ajoute les matchs de l'équipe 2
            foreach ($equipe->getMatchs2() as $match) {
                $matches[] = $match;
            }
        }

        // Supprimer les doublons en comparant les ids des matchs
        $uniqueMatches = [];
        foreach ($matches as $match) {
            $uniqueMatches[$match->getId()] = $match; // On utilise l'id comme clé
        }

        // Les valeurs du tableau $uniqueMatches contiennent les matchs sans doublon
        $matches = array_values($uniqueMatches);

        return $this->render('tournoi/affichematches.html.twig', [
            'matches' => $matches,
        ]);
    }




}
