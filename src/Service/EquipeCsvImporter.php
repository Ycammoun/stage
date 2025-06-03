<?php
namespace App\Service;

use App\Entity\Equipe;
use App\Entity\Tournoi;
use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EquipeCsvImporter
{
    private EntityManagerInterface $em;
    private UtilisateurRepository $utilisateurRepo;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $em, UtilisateurRepository $utilisateurRepo,UserPasswordHasherInterface $passwordHasher)
    {
        $this->em = $em;
        $this->utilisateurRepo = $utilisateurRepo;
        $this->passwordHasher = $passwordHasher;

    }

    public function import(string $csvPath): void
    {
        if (!file_exists($csvPath) || !is_readable($csvPath)) {
            throw new \RuntimeException("Fichier introuvable ou illisible.");
        }

        $handle = fopen($csvPath, 'r');
        $headers = fgetcsv($handle, 0, ';'); // saute l'en-tête

        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            if (empty($row[1]) || empty($row[13])) {
                continue; // une équipe doit avoir 2 joueurs
            }

            $joueur1 = $this->getOrCreateUtilisateur([
                'nom' => $row[1],
                'prenom' => $row[2],
                'fft' => $row[3],
                'dupr'=> $row[4],
                'email' => $row[11],
                'tel' => $row[12],
            ]);

            $joueur2 = $this->getOrCreateUtilisateur([
                'nom' => $row[13],
                'prenom' => $row[14],
                'fft' => $row[15],
                'dupr'=> $row[16],
                'email' => $row[23],
                'tel' => $row[24],
            ]);

            $equipe = new Equipe();
            $equipe->setNom("{$joueur1->getNom()} - {$joueur2->getNom()}");
            $equipe->addJoueur($joueur1);
            $equipe->addJoueur($joueur2);

            $this->em->persist($equipe);
        }

        fclose($handle);
        $this->em->flush();
    }
    public function importbis(string $csvPath, Tournoi $tournoi): void
    {
        if (!file_exists($csvPath) || !is_readable($csvPath)) {
            throw new \RuntimeException("Fichier introuvable ou illisible.");
        }

        $handle = fopen($csvPath, 'r');
        $headers = fgetcsv($handle, 0, ';'); // saute l'en-tête

        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            if (empty($row[1]) || empty($row[13])) {
                continue; // une équipe doit avoir 2 joueurs
            }

            $joueur1 = $this->getOrCreateUtilisateur([
                'nom' => $row[1],
                'prenom' => $row[2],
                'fft' => $row[3],
                'dupr'=> $row[4],
                'email' => $row[11],
                'tel' => $row[12],
            ]);

            $joueur2 = $this->getOrCreateUtilisateur([
                'nom' => $row[13],
                'prenom' => $row[14],
                'fft' => $row[15],
                'dupr'=> $row[16],
                'email' => $row[23],
                'tel' => $row[24],
            ]);

            $equipe = new Equipe();
            $equipe->setNom("{$joueur1->getNom()} - {$joueur2->getNom()}");
            $equipe->addJoueur($joueur1);
            $equipe->addJoueur($joueur2);
            $equipe->setTournoi($tournoi); // 👈 associe l'équipe au tournoi donné

            $this->em->persist($equipe);
        }

        fclose($handle);
        $this->em->flush();
    }

    private function getOrCreateUtilisateur(array $data): Utilisateur
    {
        $user = //$this->utilisateurRepo->findOneBy(['fft' => $data['fft']])
            //??
            $this->utilisateurRepo->findOneBy(['mail' => $data['email']]);

        if (!$user) {
            $user = new Utilisateur();
            $user->setNom($data['nom']);
            $user->setPrenom($data['prenom']);
            $user->setFft( $data['fft']);
            $user->setDupr($data['dupr']);
            $user->setMail($data['email']);
            $user->setNumero($data['tel']);

            // Valeurs par défaut obligatoires
            $user->setLogin($data['email']);
            $user->setRoles(['ROLE_JOUEUR']);
            $user->setCodepostale('00000');
            $user->setSexe('Homme');
            $user->setDateNaissance(new \DateTime('2000-01-01'));
            $hashedPassword = $this->passwordHasher->hashPassword($user, 'azerty');
            $user->setPassword($hashedPassword);

            $this->em->persist($user);
        }

        return $user;
    }
}
