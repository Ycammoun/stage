<?php

namespace App\Service;

use App\Entity\Tournoi;
use Doctrine\ORM\EntityManagerInterface;

class MatchDistributionService
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Distribue les matchs sans terrain aux terrains disponibles
     */
    public function distribution(Tournoi $tournoi): void
    {
        $terrains = $tournoi->getTerrains();

        // Récupère uniquement les matchs sans terrain et non en cours
        $matches = array_filter(
            $this->listeFinaleMatches($tournoi),
            fn($match) => $match->getTerrain() === null && !$match->isEnCours()
        );

        foreach ($matches as $match) {
            foreach ($terrains as $terrain) {
                if (!$terrain->isEstOccupé()) {
                    $match->setTerrain($terrain);
                    $match->setEnCours(true);
                    $terrain->setEstOccupé(true);

                    $this->em->persist($match);
                    $this->em->persist($terrain);
                    break;
                }
            }
        }

        $this->em->flush();
    }

    /**
     * Renvoie la liste des matchs du tournoi en alternance entre poules
     */
    public function listeFinaleMatches(Tournoi $tournoi): array
    {
        $toutesLesPoules = [];

        foreach ($tournoi->getTableaux() as $tableau) {
            foreach ($tableau->getPoules() as $poule) {
                $toutesLesPoules[] = array_values($poule->getParties()->toArray());
            }
        }

        $final = [];
        $index = 0;
        $reste = true;

        while ($reste) {
            $reste = false;
            foreach ($toutesLesPoules as $pouleMatchs) {
                if (isset($pouleMatchs[$index])) {
                    $final[] = $pouleMatchs[$index];
                    $reste = true;
                }
            }
            $index++;
        }

        return $final;
    }
    public function redistribution(Tournoi $tournoi): void
    {
        $terrains = $tournoi->getTerrains();

        // On ne prend que les matchs non joués (non en cours, sans score, sans terrain)
        $matches = array_filter(
            $this->listeFinaleMatches($tournoi),
            fn($match) =>
                $match->getTerrain() === null &&
                !$match->isEnCours() &&
                $match->getScore1() ===0 &&
                $match->getScore2() ===0
        );

        foreach ($matches as $match) {
            foreach ($terrains as $terrain) {
                if (!$terrain->isEstOccupé()) {
                    $match->setTerrain($terrain);
                    $match->setEnCours(true);
                    $terrain->setEstOccupé(true);

                    $this->em->persist($match);
                    $this->em->persist($terrain);
                    break;
                }
            }
        }

        $this->em->flush();
    }
}
