<?php
namespace App\Service;

use App\Entity\Partie;
use App\Entity\Tournoi;
use Doctrine\ORM\EntityManagerInterface;

class CreateMatche
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function createMatche(Tournoi $tournoi): void
    {
        $tableaux = $tournoi->getTableaux();
        $totalMatchsCrees = 0;

        foreach ($tableaux as $tab) {
            $poules = $tab->getPoules();

            foreach ($poules as $poule) {
                // Conversion explicite en tableau indexé pour éviter les problèmes d'index
                $equipes = $poule->getEquipes()->toArray();
                $n = count($equipes);

                for ($i = 0; $i < $n - 1; $i++) {
                    for ($j = $i + 1; $j < $n; $j++) {
                        $matche = new Partie();
                        $matche->setEquipe1($equipes[$i]);
                        $matche->setEquipe2($equipes[$j]);
                        $matche->setPoule($poule);
                        $matche->setEnCours(false);
                        $matche->setScore1(0);
                        $matche->setScore2(0);

                        $this->em->persist($matche);
                        $totalMatchsCrees++;
                    }
                }
            }
        }

        $this->em->flush();

        // Pour debug : afficher le nombre total de matchs créés (à retirer en prod)
        // echo "Total matchs créés : " . $totalMatchsCrees . PHP_EOL;
    }
}
