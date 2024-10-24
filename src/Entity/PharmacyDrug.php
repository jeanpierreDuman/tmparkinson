<?php

namespace App\Entity;

use App\Repository\PharmacyDrugRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PharmacyDrugRepository::class)]
class PharmacyDrug
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'pharmacyDrugs')]
    private ?Pharmacy $pharmacy = null;

    #[ORM\ManyToOne(inversedBy: 'pharmacyDrugs')]
    private ?Drug $drug = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column]
    private ?int $quantityToPrepare = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPharmacy(): ?Pharmacy
    {
        return $this->pharmacy;
    }

    public function setPharmacy(?Pharmacy $pharmacy): static
    {
        $this->pharmacy = $pharmacy;

        return $this;
    }

    public function getDrug(): ?Drug
    {
        return $this->drug;
    }

    public function setDrug(?Drug $drug): static
    {
        $this->drug = $drug;

        return $this;
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

    public function getQuantityToPrepare(): ?int
    {
        return $this->quantityToPrepare;
    }

    public function setQuantityToPrepare(int $quantityToPrepare): static
    {
        $this->quantityToPrepare = $quantityToPrepare;

        return $this;
    }
}
