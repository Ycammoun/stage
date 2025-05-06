<?php

namespace App\Entity;

use App\Repository\PouleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PouleRepository::class)]
class Poule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $numero = null;

    #[ORM\ManyToOne(inversedBy: 'poules')]
    private ?Tableau $tableau = null;

    /**
     * @var Collection<int, Partie>
     */
    #[ORM\OneToMany(targetEntity: Partie::class, mappedBy: 'poule')]
    private Collection $parties;

    public function __construct()
    {
        $this->parties = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(?int $numero): static
    {
        $this->numero = $numero;

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
    public function getParties(): Collection
    {
        return $this->parties;
    }

    public function addParty(Partie $party): static
    {
        if (!$this->parties->contains($party)) {
            $this->parties->add($party);
            $party->setPoule($this);
        }

        return $this;
    }

    public function removeParty(Partie $party): static
    {
        if ($this->parties->removeElement($party)) {
            // set the owning side to null (unless already changed)
            if ($party->getPoule() === $this) {
                $party->setPoule(null);
            }
        }

        return $this;
    }
}
