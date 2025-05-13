<?php
namespace App\Service;

class CalculTournoiService
{
    public function calculTournoi(int $nbJoueur, int $nbTab, int $nbTerrain)
    {
        $nbEquipeParTab = (int) ($nbJoueur / 2 / $nbTab); // On force int pour le match
        $repartitions = [];

        $cases = match ($nbEquipeParTab) {
            4 => [[4]],
            5 => [[5]],
            6 => [[6], [3, 3]],
            7 => [[4, 3]],
            8 => [[8], [4, 4]],
            9 => [[3, 3, 3]],
            10 => [[5, 5], [3, 3, 4]],
            11 => [[4, 4, 3]],
            12 => [[4, 4, 4], [3, 3, 3, 3]],
            default => []
        };

        foreach ($cases as $poules) {
            $nbMatchs = 0;
            foreach ($poules as $nbEquipes) {
                $nbMatchs += $this->nbMatch($nbEquipes);
            }

            // Supposons 10 minutes par match
            $tempsEstime = ceil($nbMatchs / $nbTerrain) * 10;

            $repartitions[] = [
                'poules' => $poules,
                'nb_matchs' => $nbMatchs,
                'temps' => $tempsEstime,
            ];
        }

        return [
            'n' => $nbEquipeParTab,
            'repartitions' => $repartitions
        ];
    }

    private function nbMatch(int $nbequipes): int
    {
        return ($nbequipes < 2) ? 0 : ($nbequipes * ($nbequipes - 1)) / 2;
    }
}