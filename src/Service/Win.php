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


    public function win(int $idPoule): array
    {
        $victoires = [];
        $poule = $this->em->getRepository(Poule::class)->find($idPoule);

        if (!$poule) {
            throw new \Exception("Poule non trouvée");
        }

        foreach ($poule->getParties() as $match) {
            $equipe1 = $match->getEquipe1();
            $equipe2 = $match->getEquipe2();
            $score1 = $match->getScore1();
            $score2 = $match->getScore2();

            foreach ([$equipe1, $equipe2] as $equipe) {
                $id = $equipe->getId();
                if (!isset($victoires[$id])) {
                    $victoires[$id] = [
                        'equipe' => $equipe,
                        'victoires' => 0,
                        'ptsMarques' => 0,
                        'ptsEncaisses' => 0,
                    ];
                }
            }

            if ($score1 > $score2) {
                $victoires[$equipe1->getId()]['victoires']++;
            } elseif ($score2 > $score1) {
                $victoires[$equipe2->getId()]['victoires']++;
            }

            $victoires[$equipe1->getId()]['ptsMarques'] += $score1;
            $victoires[$equipe1->getId()]['ptsEncaisses'] += $score2;
            $victoires[$equipe2->getId()]['ptsMarques'] += $score2;
            $victoires[$equipe2->getId()]['ptsEncaisses'] += $score1;
        }

        // Regrouper les équipes par nombre de victoires
        $groupesParVictoires = [];
        foreach ($victoires as $stat) {
            $nbVictoire = $stat['victoires'];
            $groupesParVictoires[$nbVictoire][] = $stat;
        }

        // Trier chaque groupe par critères supplémentaires
        krsort($groupesParVictoires); // Pour traiter d'abord les meilleurs

        foreach ($groupesParVictoires as &$groupe) {
            if (count($groupe) > 1) {
                usort($groupe, function ($a, $b) use ($poule) {
                    $diffA = $a['ptsMarques'] - $a['ptsEncaisses'];
                    $diffB = $b['ptsMarques'] - $b['ptsEncaisses'];

                    if ($diffB !== $diffA) {
                        return $diffB <=> $diffA;
                    }

                    if ($b['ptsMarques'] !== $a['ptsMarques']) {
                        return $b['ptsMarques'] <=> $a['ptsMarques'];
                    }

                    // Cas d'égalité parfaite — on regarde le match direct
                    $idA = $a['equipe']->getId();
                    $idB = $b['equipe']->getId();

                    foreach ($poule->getParties() as $match) {
                        $e1 = $match->getEquipe1()->getId();
                        $e2 = $match->getEquipe2()->getId();

                        if (
                            ($e1 === $idA && $e2 === $idB) ||
                            ($e1 === $idB && $e2 === $idA)
                        ) {
                            $s1 = $match->getScore1();
                            $s2 = $match->getScore2();

                            if ($e1 === $idA) {
                                return $s2 <=> $s1; // plus grand score gagne
                            } else {
                                return $s1 <=> $s2;
                            }
                        }
                    }

                    return 0; // égalité parfaite
                });
            }
        }
        unset($groupe); // bonne pratique

        // Aplatir le tableau
        $gagnants = [];
        foreach ($groupesParVictoires as $groupe) {
            foreach ($groupe as $stat) {
                $gagnants[] = $stat['equipe'];
            }
        }

        return $gagnants;
    }


}
