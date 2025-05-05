<?php

namespace App\DataFixtures;

use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $utilisateur= new Utilisateur();
        $utilisateur
            ->setNom('Rathier')
            ->setPrenom('Pascal')
            ->setDateNaissance(new \DateTime('1970-01-01'))
            ->setLogin('pascalr')
            ->setPassword('azerty');

        $manager->flush();
    }
}
