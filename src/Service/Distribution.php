<?php
namespace App\Service;

use App\Entity\Tournoi;
use Doctrine\ORM\EntityManagerInterface;

class Distribution
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getRepartitions(): array
    {
        $dernierTournoi = $this->em->getRepository(Tournoi::class)->findOneBy([], ['id' => 'DESC']);
        $dernierTableaux = $dernierTournoi->getTableaux()->last();
        $equipes = $dernierTableaux->getEquipes();
        $n = $equipes->count();

        if ($n < 4) {
            return ['error' => "Pas assez d'équipes (minimum 4 requises)"];
        }

        $repartitions = [];

        $cases = match ($n) {
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
            $tempsEstime = $nbMatchs * 10;
            $repartitions[] = [
                'poules' => $poules,
                'nb_matchs' => $nbMatchs,
                'temps' => $tempsEstime,
            ];
        }

        return ['n' => $n, 'repartitions' => $repartitions];
    }

    private function nbMatch($nbequipes): int
    {
        return ($nbequipes < 2) ? 0 : ($nbequipes * ($nbequipes - 1)) / 2;
    }
}