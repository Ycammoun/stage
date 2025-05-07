<?php

namespace App\DataFixtures;

use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Utilisateur 1
        $utilisateur1 = new Utilisateur();
        $utilisateur1
            ->setNom('Rathier')
            ->setPrenom('Pascal')
            ->setDateNaissance(new \DateTime('1970-01-01'))
            ->setLogin('pascalr')
            ->setSexe('Homme')
            ->setRoles(['ROLE_ADMIN'])
            ->setMail('pascal@mail.com')
            ->setCodepostale('86000')
            ->setNumero('0600000000');

        $utilisateur1->setPassword(
            $this->passwordHasher->hashPassword($utilisateur1, 'azerty')
        );

        // Utilisateur 2
        $utilisateur2 = new Utilisateur();
        $utilisateur2
            ->setNom('Dupont')
            ->setPrenom('Marie')
            ->setDateNaissance(new \DateTime('1985-02-15'))
            ->setLogin('maried')
            ->setSexe('Femme')
            ->setMail('marie@mail.com')
            ->setCodepostale('75000')
            ->setRoles(['ROLE_JOUEUR'])
            ->setNumero('0601234567');

        $utilisateur2->setPassword(
            $this->passwordHasher->hashPassword($utilisateur2, '123456')
        );

        // Utilisateur 3
        $utilisateur3 = new Utilisateur();
        $utilisateur3
            ->setNom('Martin')
            ->setPrenom('Lucas')
            ->setDateNaissance(new \DateTime('1992-07-20'))
            ->setLogin('lucasm')
            ->setSexe('Homme')
            ->setMail('lucas@mail.com')
            ->setCodepostale('69000')
            ->setRoles(['ROLE_JOUEUR'])
            ->setNumero('0612345678');

        $utilisateur3->setPassword(
            $this->passwordHasher->hashPassword($utilisateur3, 'motdepasse')
        );

        // Persistance
        $manager->persist($utilisateur1);
        $manager->persist($utilisateur2);
        $manager->persist($utilisateur3);
        $manager->flush();
    }
}
