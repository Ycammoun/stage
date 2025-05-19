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
                    break; // on passe au match suivant
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
}
