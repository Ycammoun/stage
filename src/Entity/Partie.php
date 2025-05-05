<?php

namespace App\Entity;

use App\Repository\PartieRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PartieRepository::class)]
class Partie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTime $heurDebut = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTime $heurFin = null;

    #[ORM\Column]
    private ?int $pause = null;

    #[ORM\Column]
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
