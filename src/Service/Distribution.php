<?php
namespace App\Service;

use App\Entity\Equipe;
use App\Entity\Tournoi;
use Doctrine\ORM\EntityManagerInterface;

class Distribution
{
    private EntityManagerInterface $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    public function distribution()
    {
        $dernierTournoi = $this->em->getRepository(Tournoi::class)->findOneBy([], ['id' => 'DESC']);
        $dernierTableaux = $dernierTournoi->getTableaux()->last();
        $equipes = $dernierTableaux->getEquipes();
        $n = $equipes->count();

        if ($n < 4) {
            printf("Pas assez d'équipes (minimum 4 requises)\n");
            return;
        }

        printf("Nombre total d'équipes : %d\n", $n);
        printf("Répartitions possibles :\n");

        switch ($n) {
            case 4:
                $this->afficherPoule([4]);
                break;
            case 5:
                $this->afficherPoule([5]);
                break;
            case 6:
                $this->afficherPoule([6]);
                $this->afficherPoule([3, 3]);
                break;
            case 7:
                $this->afficherPoule([4, 3]);
                break;
            case 8:
                $this->afficherPoule([8]);
                $this->afficherPoule([4, 4]);
                break;
            case 9:
                $this->afficherPoule([3, 3, 3]);
                break;
            case 10:
                $this->afficherPoule([5, 5]);
                $this->afficherPoule([3, 3, 4]);
                break;
            case 11:
                $this->afficherPoule([4, 4, 3]);
                break;
            case 12:
                $this->afficherPoule([4, 4, 4]);
                $this->afficherPoule([3, 3, 3, 3]);
                break;
            default:
                printf("- Répartition personnalisée nécessaire pour %d équipes\n", $n);
                break;
        }
    }
    private function afficherPoule(array $poules)
    {
        $nbMatchsTotal = 0;
        printf("- %d poule(s) : ", count($poules));
        foreach ($poules as $i => $nbEquipes) {
            printf("%d équipe(s)%s", $nbEquipes, $i < count($poules) - 1 ? " / " : "");
            $nbMatchsTotal += $this->nbMatch($nbEquipes);
        }
        $tempsTotal = $nbMatchsTotal * 10;
        printf(" => %d match(s), temps estimé : %d min\n", $nbMatchsTotal, $tempsTotal);
    }


    public function nbMatch($nbequipes)
    {
        if ($nbequipes < 2) {
            return 0;
        }

        return ($nbequipes * ($nbequipes - 1)) / 2;

    }

}