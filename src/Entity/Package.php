<?php

namespace App\Entity;

use App\Repository\PackageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PackageRepository::class)]
class Package
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column]
    private ?int $quantityDrug = null;

    /**
     * @var Collection<int, Drug>
     */
    #[ORM\OneToMany(targetEntity: Drug::class, mappedBy: 'package')]
    private Collection $drugs;

    public function __construct()
    {
        $this->drugs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getQuantityDrug(): ?int
    {
        return $this->quantityDrug;
    }

    public function setQuantityDrug(int $quantityDrug): static
    {
        $this->quantityDrug = $quantityDrug;

        return $this;
    }

    /**
     * @return Collection<int, Drug>
     */
    public function getDrugs(): Collection
    {
        return $this->drugs;
    }

    public function addDrug(Drug $drug): static
    {
        if (!$this->drugs->contains($drug)) {
            $this->drugs->add($drug);
            $drug->setPackage($this);
        }

        return $this;
    }

    public function removeDrug(Drug $drug): static
    {
        if ($this->drugs->removeElement($drug)) {
            // set the owning side to null (unless already changed)
            if ($drug->getPackage() === $this) {
                $drug->setPackage(null);
            }
        }

        return $this;
    }

    public function __toString() {
        return "Boite de " . $this->quantityDrug . " médicaments";
    }
}
