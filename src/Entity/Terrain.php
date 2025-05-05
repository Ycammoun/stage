<?php

namespace App\Entity;

use App\Repository\TerrainRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TerrainRepository::class)]
class Terrain
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $estOccupé = null;

    #[ORM\Column(nullable: true)]
    private ?int $longueur = null;

    #[ORM\Column(nullable: true)]
    private ?int $largeur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isEstOccupé(): ?bool
    {
        return $this->estOccupé;
    }

    public function setEstOccupé(bool $estOccupé): static
    {
        $this->estOccupé = $estOccupé;

        return $this;
    }

    public function getLongueur(): ?int
    {
        return $this->longueur;
    }

    public function setLongueur(?int $longueur): static
    {
        $this->longueur = $longueur;

        return $this;
    }

    public function getLargeur(): ?int
    {
        return $this->largeur;
    }

    public function setLargeur(?int $largeur): static
    {
        $this->largeur = $largeur;

        return $this;
    }
}
