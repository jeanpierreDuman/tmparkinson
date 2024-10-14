<?php

namespace App\Entity;

use App\Repository\PrescriptionLineRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PrescriptionLineRepository::class)]
class PrescriptionLine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'prescriptionLines')]
    private ?Drug $drug = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\ManyToOne(inversedBy: 'prescriptionLines')]
    private ?Prescription $prescription = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $frequency = [];

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPrescription(): ?Prescription
    {
        return $this->prescription;
    }

    public function setPrescription(?Prescription $prescription): static
    {
        $this->prescription = $prescription;

        return $this;
    }

    public function getFrequency(): array
    {
        return $this->frequency;
    }

    public function setFrequency(array $frequency): static
    {
        $this->frequency = $frequency;

        return $this;
    }
}
