<?php
namespace App\Service;


use App\Entity\Partie;
use Doctrine\ORM\EntityManagerInterface;

class setScore
{
    private EntityManagerInterface $em;
    public function __construct(EntityManagerInterface $em){
        $this->em=$em;
    }
    public function setScore(int $score1, int $score2,Partie $matche):void
    {
        $matche->setScore1($score1);
        $matche->setScore2($score2);
        $matche->setEnCours(false);
        $this->em->persist($matche);
        $this->em->flush();



    }
}