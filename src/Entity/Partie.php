<?php

namespace App\Entity;

use App\Repository\PartieRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Table(name: 'Match')]
#[ORM\Entity(repositoryClass: PartieRepository::class)]
class Partie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE,name: 'date_match',nullable: true)]
    private ?\DateTime $date = null;

    #[ORM\Column(type: Types::TIME_MUTABLE,name: 'heur_debut',nullable: true)]
    private ?\DateTime $heurDebut = null;



    #[ORM\Column(type: Types::BOOLEAN,name: 'match_en_cours')]
    private ?bool $enCours = null;

    #[ORM\ManyToOne(inversedBy: 'matchs1')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Equipe $equipe1 = null;

    #[ORM\ManyToOne(inversedBy: 'matchs2')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Equipe $equipe2 = null;

    #[ORM\Column]
    private ?int $score1 = null;

    #[ORM\Column]
    private ?int $score2 = null;

    #[ORM\ManyToOne(inversedBy: 'parties')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Poule $poule = null;

    #[ORM\Column(nullable: true,name: 'valide')]
    private ?bool $isValideParAdversaire = null;

    #[ORM\ManyToOne(inversedBy: 'parties')]
    #[ORM\JoinColumn(nullable: true)]

    private ?Terrain $terrain = null;

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

    public function getHeurDebut(): ?\DateTime
    {
        return $this->heurDebut;
    }

    public function setHeurDebut(\DateTime $heurDebut): static
    {
        $this->heurDebut = $heurDebut;

        return $this;
    }


    public function isEnCours(): ?bool
    {
        return $this->enCours;
    }

    public function setEnCours(bool $enCours): static
    {
        $this->enCours = $enCours;

        return $this;
    }

    public function getEquipe1(): ?Equipe
    {
        return $this->equipe1;
    }

    public function setEquipe1(?Equipe $equipe1): static
    {
        $this->equipe1 = $equipe1;

        return $this;
    }

    public function getEquipe2(): ?Equipe
    {
        return $this->equipe2;
    }

    public function setEquipe2(?Equipe $equipe2): static
    {
        $this->equipe2 = $equipe2;

        return $this;
    }

    public function getScore1(): ?int
    {
        return $this->score1;
    }

    public function setScore1(int $score1): static
    {
        $this->score1 = $score1;

        return $this;
    }

    public function getScore2(): ?int
    {
        return $this->score2;
    }

    public function setScore2(int $score2): static
    {
        $this->score2 = $score2;

        return $this;
    }

    public function getPoule(): ?Poule
    {
        return $this->poule;
    }

    public function setPoule(?Poule $poule): static
    {
        $this->poule = $poule;

        return $this;
    }

    public function isValideParAdversaire(): ?bool
    {
        return $this->isValideParAdversaire;
    }

    public function setIsValideParAdversaire(?bool $isValideParAdversaire): static
    {
        $this->isValideParAdversaire = $isValideParAdversaire;

        return $this;
    }

    public function getTerrain(): ?Terrain
    {
        return $this->terrain;
    }

    public function setTerrain(?Terrain $terrain): static
    {
        $this->terrain = $terrain;

        return $this;
    }
}
