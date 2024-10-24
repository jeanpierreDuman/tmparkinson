<?php

namespace App\Entity;

use App\Repository\PharmacyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PharmacyRepository::class)]
class Pharmacy
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'pharmacy')]
    private Collection $users;

    /**
     * @var Collection<int, PharmacyDrug>
     */
    #[ORM\OneToMany(targetEntity: PharmacyDrug::class, mappedBy: 'pharmacy')]
    private Collection $pharmacyDrugs;

    public function __construct()
    {
        $this->users = new ArrayCollection();
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
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setPharmacy($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getPharmacy() === $this) {
                $user->setPharmacy(null);
            }
        }

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
            $pharmacyDrug->setPharmacy($this);
        }

        return $this;
    }

    public function removePharmacyDrug(PharmacyDrug $pharmacyDrug): static
    {
        if ($this->pharmacyDrugs->removeElement($pharmacyDrug)) {
            // set the owning side to null (unless already changed)
            if ($pharmacyDrug->getPharmacy() === $this) {
                $pharmacyDrug->setPharmacy(null);
            }
        }

        return $this;
    }
}