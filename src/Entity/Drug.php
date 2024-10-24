<?php

namespace App\Entity;

use App\Repository\DrugRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DrugRepository::class)]
class Drug
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, PrescriptionLine>
     */
    #[ORM\OneToMany(targetEntity: PrescriptionLine::class, mappedBy: 'drug')]
    private Collection $prescriptionLines;

    #[ORM\Column]
    private ?int $milligram = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column]
    private ?int $quantityPackage = null;

    /**
     * @var Collection<int, PharmacyDrug>
     */
    #[ORM\OneToMany(targetEntity: PharmacyDrug::class, mappedBy: 'drug')]
    private Collection $pharmacyDrugs;

    public function __construct()
    {
        $this->prescriptionLines = new ArrayCollection();
        $this->pharmacyDrugs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, PrescriptionLine>
     */
    public function getPrescriptionLines(): Collection
    {
        return $this->prescriptionLines;
    }

    public function addPrescriptionLine(PrescriptionLine $prescriptionLine): static
    {
        if (!$this->prescriptionLines->contains($prescriptionLine)) {
            $this->prescriptionLines->add($prescriptionLine);
            $prescriptionLine->setDrug($this);
        }

        return $this;
    }

    public function removePrescriptionLine(PrescriptionLine $prescriptionLine): static
    {
        if ($this->prescriptionLines->removeElement($prescriptionLine)) {
            // set the owning side to null (unless already changed)
            if ($prescriptionLine->getDrug() === $this) {
                $prescriptionLine->setDrug(null);
            }
        }

        return $this;
    }

    public function getMilligram(): ?int
    {
        return $this->milligram;
    }

    public function setMilligram(int $milligram): static
    {
        $this->milligram = $milligram;

        return $this;
    }

    public function __toString()
    {
        return $this->name . " " . $this->milligram . " G " . $this->toStringType();
    }

    public function toStringType() {
        if($this->type === "comp") {
            return "Comprimé";
        } else {
            return "Gélule";
        }
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getQuantityPackage(): ?int
    {
        return $this->quantityPackage;
    }

    public function setQuantityPackage(int $quantityPackage): static
    {
        $this->quantityPackage = $quantityPackage;

        return $this;
    }

    /**
     * @return Collection<int, PharmacyDrug>
     */
    public function getPharmacyDrugs(): Collection
    {
        return $this->pharmacyDrugs;
    }

    public function addPharmacyDrug(PharmacyDrug $pharmacyDrug): static
    {
        if (!$this->pharmacyDrugs->contains($pharmacyDrug)) {
            $this->pharmacyDrugs->add($pharmacyDrug);
            $pharmacyDrug->setDrug($this);
        }

        return $this;
    }

    public function removePharmacyDrug(PharmacyDrug $pharmacyDrug): static
    {
        if ($this->pharmacyDrugs->removeElement($pharmacyDrug)) {
            // set the owning side to null (unless already changed)
            if ($pharmacyDrug->getDrug() === $this) {
                $pharmacyDrug->setDrug(null);
            }
        }

        return $this;
    }
}