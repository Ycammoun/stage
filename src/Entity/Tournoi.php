<?php

namespace App\Entity;

use App\Repository\TournoiRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Table(name: 'Tournoi')]
#[ORM\Entity(repositoryClass: TournoiRepository::class)]
class Tournoi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE,name: 'date_tournoi')]
    private ?\DateTime $date = null;

    #[ORM\Column(length: 255)]
    private ?string $intitule = null;

    /**
     * @var Collection<int, Tableau>
     */
    #[ORM\OneToMany(targetEntity: Tableau::class, mappedBy: 'tournoi',cascade: [ 'remove'])]
    private Collection $tableaux;

    #[ORM\Column(name: 'nombre_terrain', type: Types::INTEGER,nullable: true)]
    private ?int $nbStade = null;

    /**
     * @var Collection<int, Terrain>
     */
    #[ORM\OneToMany(targetEntity: Terrain::class, mappedBy: 'tournoi',cascade: ['remove'])]
    private Collection $terrains;

    /**
     * @var Collection<int, Equipe>
     */
    #[ORM\OneToMany(targetEntity: Equipe::class, mappedBy: 'tournoi',cascade: ['remove'])]
    private Collection $equipes;



    public function __construct()
    {
        $this->tableaux = new ArrayCollection();
        $this->terrains = new ArrayCollection();
        $this->equipes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getIntitule(): ?string
    {
        return $this->intitule;
    }

    public function setIntitule(string $intitule): static
    {
        $this->intitule = $intitule;

        return $this;
    }

    /**
     * @return Collection<int, Tableau>
     */
    public function getTableaux(): Collection
    {
        return $this->tableaux;
    }

    public function addTableaux(Tableau $tableaux): static
    {
        if (!$this->tableaux->contains($tableaux)) {
            $this->tableaux->add($tableaux);
            $tableaux->setTournoi($this);
        }

        return $this;
    }

    public function removeTableaux(Tableau $tableaux): static
    {
        if ($this->tableaux->removeElement($tableaux)) {
            // set the owning side to null (unless already changed)
            if ($tableaux->getTournoi() === $this) {
                $tableaux->setTournoi(null);
            }
        }

        return $this;
    }

    public function getNbStade(): ?int
    {
        return $this->nbStade;
    }

    public function setNbStade(int $nbStade): static
    {
        $this->nbStade = $nbStade;

        return $this;
    }

    /**
     * @return Collection<int, Terrain>
     */
    public function getTerrains(): Collection
    {
        return $this->terrains;
    }

    public function addTerrain(Terrain $terrain): static
    {
        if (!$this->terrains->contains($terrain)) {
            $this->terrains->add($terrain);
            $terrain->setTournoi($this);
        }

        return $this;
    }

    public function removeTerrain(Terrain $terrain): static
    {
        if ($this->terrains->removeElement($terrain)) {
            // set the owning side to null (unless already changed)
            if ($terrain->getTournoi() === $this) {
                $terrain->setTournoi(null);
            }
        }

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
            $equipe->setTournoi($this);
        }

        return $this;
    }

    public function removeEquipe(Equipe $equipe): static
    {
        if ($this->equipes->removeElement($equipe)) {
            // set the owning side to null (unless already changed)
            if ($equipe->getTournoi() === $this) {
                $equipe->setTournoi(null);
            }
        }

        return $this;
    }


}
