<?php

namespace App\Entity;

use App\Repository\PrescriptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PrescriptionRepository::class)]
class Prescription
{
    const PRESCRIPTION_PREPARATION = "preparation";
    const PRESCRIPTION_RECEIVE = "receive";
    const PRESCRIPTION_COMPLETE = "complete";

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    public ?bool $isComplete = false;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateStart = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateEnd = null;

    /**
     * @var Collection<int, PrescriptionLine>
     */
    #[ORM\OneToMany(targetEntity: PrescriptionLine::class, mappedBy: 'prescription', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $prescriptionLines;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'prescriptions')]
    private ?User $user = null;

    #[ORM\Column]
    private ?bool $isWaitingPharmacyForDrug = false;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateReceipt = null;

    public function __construct()
    {
        $this->prescriptionLines = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isComplete(): ?bool
    {
        return $this->isComplete;
    }

    public function setComplete(bool $isComplete): static
    {
        $this->isComplete = $isComplete;

        return $this;
    }

    public function getDateStart(): ?\DateTimeInterface
    {
        return $this->dateStart;
    }

    public function setDateStart(\DateTimeInterface $dateStart): static
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->dateEnd;
    }

    public function setDateEnd(\DateTimeInterface $dateEnd): static
    {
        $this->dateEnd = $dateEnd;

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
            $prescriptionLine->setPrescription($this);
        }

        return $this;
    }

    public function removePrescriptionLine(PrescriptionLine $prescriptionLine): static
    {
        if ($this->prescriptionLines->removeElement($prescriptionLine)) {
            // set the owning side to null (unless already changed)
            if ($prescriptionLine->getPrescription() === $this) {
                $prescriptionLine->setPrescription(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function isWaitingPharmacyForDrug(): ?bool
    {
        return $this->isWaitingPharmacyForDrug;
    }

    public function setWaitingPharmacyForDrug(bool $isWaitingPharmacyForDrug): static
    {
        $this->isWaitingPharmacyForDrug = $isWaitingPharmacyForDrug;

        return $this;
    }

    public function getDateReceipt(): ?\DateTimeInterface
    {
        return $this->dateReceipt;
    }

    public function setDateReceipt(?\DateTimeInterface $dateReceipt): static
    {
        $this->dateReceipt = $dateReceipt;

        return $this;
    }
}
