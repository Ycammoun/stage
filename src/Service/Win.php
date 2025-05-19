<?php
namespace App\Service;

use App\Entity\Equipe;
use App\Entity\Poule;
use Doctrine\ORM\EntityManagerInterface;

class Win
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function win(Poule $poule): ?Equipe
    {
        $matchs = $poule->getMatchs();
        $victoires = []; // [idEquipe => ['equipe' => Equipe, 'victoires' => int, 'match' => Match]]

        foreach ($matchs as $match) {
            $score1 = $match->getScore1();
            $score2 = $match->getScore2();

            if ($score1 === null || $score2 === null) {
                continue; // match non joué
            }

            if ($score1 > $score2) {
                $gagnant = $match->getEquipe1();
                $scoreAdversaire = $score2;
            } elseif ($score2 > $score1) {
                $gagnant = $match->getEquipe2();
                $scoreAdversaire = $score1;
            } else {
                continue; // match nul
            }

            $id = $gagnant->getId();

            if (!isset($victoires[$id])) {
                $victoires[$id] = [
                    'equipe' => $gagnant,
                    'victoires' => 1,
                    'match' => $match,
                    'scoreAdversaire' => $scoreAdversaire
                ];
            } else {
                $victoires[$id]['victoires']++;

                // Si ce match a un adversaire avec un score plus bas, on garde ce match
                if ($scoreAdversaire < $victoires[$id]['scoreAdversaire']) {
                    $victoires[$id]['match'] = $match;
                    $victoires[$id]['scoreAdversaire'] = $scoreAdversaire;
                }
            }
        }

        if (empty($victoires)) {
            return null; // aucun gagnant
        }

        $maxVictoires = max(array_column($victoires, 'victoires'));

        $exaequo = array_filter($victoires, fn($v) => $v['victoires'] === $maxVictoires);

        if (count($exaequo) === 1) {
            return array_values($exaequo)[0]['equipe'];
        } else {
            $meilleurEquipe = null;
            $minScoreAdversaire = PHP_INT_MAX;

            foreach ($exaequo as $data) {
                if ($data['scoreAdversaire'] < $minScoreAdversaire) {
                    $minScoreAdversaire = $data['scoreAdversaire'];
                    $meilleurEquipe = $data['equipe'];
                }
            }

            return $meilleurEquipe;
        }
    }
}
