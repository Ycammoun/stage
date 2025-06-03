<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;  // Assurez-vous d'importer Collection


#[ORM\Table(name: 'Utilisateur')]
#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_LOGIN', fields: ['login'])]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180,type: 'string', unique: true,name: 'login')]
    private ?string $login = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(name: 'mot_de_passe',type: 'string')]
    private ?string $password = null;

    #[ORM\Column(length: 255,name: 'nom')]
    private ?string $nom = null;

    #[ORM\Column(length: 255,name: 'prenom')]
    private ?string $prenom = null;

    #[ORM\Column(type: 'date',name: 'date_naissance')]
    private ?\DateTime $dateNaissance = null;

    #[ORM\Column(length: 255,name: 'mail',unique: true)]
    private ?string $mail = null;

    #[ORM\Column(length: 255,name: 'numero_de_téléphone',unique: true)]
    private ?string $numero = null;

    #[ORM\Column(length: 255,name: 'code_postal')]
    private ?string $codepostale = null;

    #[ORM\Column(length: 255)]
    private ?string $sexe = null;

    #[ORM\ManyToMany(targetEntity: Equipe::class, mappedBy: 'joueurs', cascade: ['persist'])]
    private Collection $equipes;

    #[ORM\Column(nullable: true)]
    private ?string $dupr = null;

    #[ORM\Column(nullable: true)]
    private ?string $fft = null;

    public function __construct()
    {
        $this->equipes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): static
    {
        $this->login = $login;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->login;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getDateNaissance(): ?\DateTime
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(\DateTime $dateNaissance): static
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): static
    {
        $this->mail = $mail;

        return $this;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): static
    {
        $this->numero = $numero;

        return $this;
    }

    public function getCodepostale(): ?string
    {
        return $this->codepostale;
    }

    public function setCodepostale(string $codepostale): static
    {
        $this->codepostale = $codepostale;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): static
    {
        $this->sexe = $sexe;

        return $this;
    }
    /**
     * @return Collection<int, Equipe>
     */
    public function getEquipes(): Collection
    {
        return $this->equipes;
    }

    public function addEquipe(Equipe $equipe): static
    {
        if (!$this->equipes->contains($equipe)) {
            $this->equipes->add($equipe);
        }

        return $this;
    }

    public function removeEquipe(Equipe $equipe): static
    {
        $this->equipes->removeElement($equipe);

        return $this;
    }

    public function getDupr(): ?string
    {
        return $this->dupr;
    }

    public function setDupr(?string $dupr): static
    {
        $this->dupr = $dupr;
        return $this;
    }

    public function getFft(): ?string
    {
        return $this->fft;
    }

    public function setFft(?string $fft): static
    {
        $this->fft = $fft;
        return $this;
    }

}
