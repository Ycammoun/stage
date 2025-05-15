<?php

namespace App\Service;

use App\Entity\Tournoi;
use Doctrine\ORM\EntityManagerInterface;

class MatchDistributionService
{
    private EntityManagerInterface $em;
    private array $listeAttente = [];

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->listeAttente = [];
    }

    /**
     * Distribue les matchs aux terrains disponibles
     */
    public function distribution(Tournoi $tournoi): void
    {
        $terrains = $tournoi->getTerrains();
        $matches = $this->listeFinaleMatches($tournoi);

        $this->listeAttente = [];

        foreach ($matches as $match) {
            // Si le match a déjà un terrain attribué, on ne le traite pas ici
            if ($match->getTerrain() !== null) {
                continue;
            }

            $terrainTrouve = false;

            foreach ($terrains as $terrain) {
                if (!$terrain->isEstOccupé()) {
                    $match->setTerrain($terrain);
                    $terrain->setEstOccupé(true);

                    $this->em->persist($match);
                    $this->em->persist($terrain);

                    $terrainTrouve = true;
                    break;
                }
            }

            if (!$terrainTrouve) {
                // On ajoute le match à la liste d'attente
                $this->listeAttente[] = $match;
            }
        }

        $this->em->flush();

        // Re-essayer la distribution pour les matchs en attente
        if (!empty($this->listeAttente)) {
            $this->reEssayerDistribution($this->listeAttente, $tournoi);
        }
    }

    /**
     * Réessaye de distribuer les matchs en attente
     */
    public function reEssayerDistribution(array $matchesEnAttente, Tournoi $tournoi): void
    {
        $terrains = $tournoi->getTerrains();
        $enAttente = $matchesEnAttente;

        do {
            $initialCount = count($enAttente);
            $restants = [];

            foreach ($enAttente as $match) {
                $terrainTrouve = false;

                foreach ($terrains as $terrain) {
                    if (!$terrain->isEstOccupé()) {
                        $match->setTerrain($terrain);
                        $terrain->setEstOccupé(true);

                        $this->em->persist($match);
                        $this->em->persist($terrain);

                        $terrainTrouve = true;
                        break;
                    }
                }

                if (!$terrainTrouve) {
                    $restants[] = $match;
                }
            }

            $this->em->flush();
            $enAttente = $restants;

        } while (count($enAttente) < $initialCount && count($enAttente) > 0);
    }

    /**
     * Renvoie la liste des matchs du tournoi en respectant l'ordre d'alternance entre poules
     */
    public function listeFinaleMatches(Tournoi $tournoi): array
    {
        $toutesLesPoules = [];

        // Récupère les matchs de chaque poule dans un tableau séparé
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
