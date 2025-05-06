<?php

namespace App\Entity;

use App\Repository\EquipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Utilisateur;

#[ORM\Entity(repositoryClass: EquipeRepository::class)]
#[ORM\Table(name: 'equipe')]
class Equipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, type: Types::STRING)]
    private ?string $nom = null;

    #[ORM\ManyToMany(targetEntity: Utilisateur::class,inversedBy: 'equipes')]
    #[ORM\JoinTable(name: 'equipe_utilisateur')]
    private Collection $joueurs;

    #[ORM\ManyToOne(inversedBy: 'equipes')]
    private ?Tableau $tableau = null;

    /**
     * @var Collection<int, Partie>
     */
    #[ORM\OneToMany(targetEntity: Partie::class, mappedBy: 'equipe1')]
    private Collection $matchs1;

    /**
     * @var Collection<int, Partie>
     */
    #[ORM\OneToMany(targetEntity: Partie::class, mappedBy: 'equipe2')]
    private Collection $matchs2;

    public function __construct()
    {
        $this->joueurs = new ArrayCollection();
        $this->matchs1 = new ArrayCollection();
        $this->matchs2 = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection<int, Utilisateur>
     */
    public function getJoueurs(): Collection
    {
        return $this->joueurs;
    }

    public function addJoueur(Utilisateur $joueur): static
    {
        if (!$this->joueurs->contains($joueur) && $this->joueurs->count() < 2) {
            $this->joueurs->add($joueur);
        }

        return $this;
    }

    public function removeJoueur(Utilisateur $joueur): static
    {
        $this->joueurs->removeElement($joueur);
        return $this;
    }

    public function getTableau(): ?Tableau
    {
        return $this->tableau;
    }

    public function setTableau(?Tableau $tableau): static
    {
        $this->tableau = $tableau;

        return $this;
    }

    /**
     * @return Collection<int, Partie>
     */
    public function getMatchs1(): Collection
    {
        return $this->matchs1;
    }

    public function addMatchs1(Partie $matchs1): static
    {
        if (!$this->matchs1->contains($matchs1)) {
            $this->matchs1->add($matchs1);
            $matchs1->setEquipe1($this);
        }

        return $this;
    }

    public function removeMatchs1(Partie $matchs1): static
    {
        if ($this->matchs1->removeElement($matchs1)) {
            // set the owning side to null (unless already changed)
            if ($matchs1->getEquipe1() === $this) {
                $matchs1->setEquipe1(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Partie>
     */
    public function getMatchs2(): Collection
    {
        return $this->matchs2;
    }

    public function addMatchs2(Partie $matchs2): static
    {
        if (!$this->matchs2->contains($matchs2)) {
            $this->matchs2->add($matchs2);
            $matchs2->setEquipe2($this);
        }

        return $this;
    }

    public function removeMatchs2(Partie $matchs2): static
    {
        if ($this->matchs2->removeElement($matchs2)) {
            // set the owning side to null (unless already changed)
            if ($matchs2->getEquipe2() === $this) {
                $matchs2->setEquipe2(null);
            }
        }

        return $this;
    }
}
