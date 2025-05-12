<?php
namespace App\Service;

use App\Entity\Partie;
use App\Entity\Poule;
use Doctrine\ORM\EntityManagerInterface;

class CreateMatche
{
    private EntityManagerInterface $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    public function createMatche()
    {
        $poule = $this->em->getRepository(Poule::class)->findOneBy([], ['id' => 'DESC']);
        $equipes = $poule->getEquipes();
        $n = $equipes->count();
        for ($i = 0; $i < $n - 1; $i++) {
            for ($j = $i + 1; $j < $n; $j++) {
                $matche = new Partie();
                $matche->setEquipe1($equipes[$i]);
                $matche->setEquipe2($equipes[$j]);
                $this->em->persist($matche);
                $this->em->flush();
            }
        }
    }




}