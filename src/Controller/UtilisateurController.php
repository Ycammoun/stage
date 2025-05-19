<?php

namespace App\Controller;

use App\Entity\Equipe;
use App\Entity\Partie;
use App\Form\setScoreForm;
use App\Service\MatchDistributionService;
use App\Service\setScore;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
            $terrain=$matche->getTerrain();

            $matche->setTerrain(NULL);
            $matche->setEnCours(false); // <-- ligne manquante ici !

            if ($terrain !== null) {
                $terrain->setEstOccupé(false);
                $entityManager->persist($terrain);
            }
            $entityManager->persist($matche);
            $entityManager->flush();
            $redistribut->distribution($matche->getPoule()->getTableau()->getTournoi());

            return $this->redirectToRoute('utilisateur_affichematches');
        }


        return $this->render('tournoi/setScore.html.twig', ['matche' => $matche, 'form' => $form->createView(), 'message' => '']);
    }
    #[Route('/validerScore/{idMatche}', name: '_valider_score')]
    public function validerScore(EntityManagerInterface $entityManager, int $idMatche): Response
    {
        $user = $this->getUser();
        $matche = $entityManager->getRepository(Partie::class)->find($idMatche);

        if (!$matche) {
            throw $this->createNotFoundException("Match introuvable.");
        }

        $equipe2 = $matche->getEquipe2();

        // Vérifie si l'utilisateur est bien un joueur de l'équipe 2
        $autorise = false;
        foreach ($equipe2->getJoueurs() as $joueur) {
            if ($joueur === $user) {
                $autorise = true;
                break;
            }
        }

        if (!$autorise) {
            throw $this->createAccessDeniedException("Vous n'êtes pas autorisé à valider ce score.");
        }

        // Le score est validé
        $matche->setIsValideParAdversaire(true);
        $entityManager->flush();

        return $this->redirectToRoute('utilisateur_affichematches');
    }
}
