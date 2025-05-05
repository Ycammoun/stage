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

    #[ORM\Column(type: Types::DATE_MUTABLE,name: 'date_match')]
    private ?\DateTime $date = null;

    #[ORM\Column(type: Types::TIME_MUTABLE,name: 'heur_debut')]
    private ?\DateTime $heurDebut = null;

    #[ORM\Column(type: Types::TIME_MUTABLE,name: 'heur_fin')]
    private ?\DateTime $heurFin = null;

    #[ORM\Column(name: 'durée_pause',type: Types::INTEGER)]
    private ?int $pause = null;

    #[ORM\Column(type: Types::BOOLEAN,name: 'match_en_cours')]
    private ?bool $enCours = null;

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

    public function getHeurFin(): ?\DateTime
    {
        return $this->heurFin;
    }

    public function setHeurFin(\DateTime $heurFin): static
    {
        $this->heurFin = $heurFin;

        return $this;
    }

    public function getPause(): ?int
    {
        return $this->pause;
    }

    public function setPause(int $pause): static
    {
        $this->pause = $pause;

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
}
