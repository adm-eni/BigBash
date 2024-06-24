<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $phoneNumber = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Campus $campus = null;

    /**
     * @var Collection<int, Outing>
     */
    #[ORM\OneToMany(targetEntity: Outing::class, mappedBy: 'host')]
    private Collection $hostedOuting;

    /**
     * @var Collection<int, Outing>
     */
    #[ORM\ManyToMany(targetEntity: Outing::class, inversedBy: 'attendees')]
    private Collection $enteredOuting;

    public function __construct()
    {
        $this->hostedOuting = new ArrayCollection();
        $this->enteredOuting = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): static
    {
        $this->campus = $campus;

        return $this;
    }

    /**
     * @return Collection<int, Outing>
     */
    public function getHostedOuting(): Collection
    {
        return $this->hostedOuting;
    }

    public function addHostedOuting(Outing $hostedOuting): static
    {
        if (!$this->hostedOuting->contains($hostedOuting)) {
            $this->hostedOuting->add($hostedOuting);
            $hostedOuting->setHost($this);
        }

        return $this;
    }

    public function removeHostedOuting(Outing $hostedOuting): static
    {
        if ($this->hostedOuting->removeElement($hostedOuting)) {
            // set the owning side to null (unless already changed)
            if ($hostedOuting->getHost() === $this) {
                $hostedOuting->setHost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Outing>
     */
    public function getEnteredOuting(): Collection
    {
        return $this->enteredOuting;
    }

    public function addEnteredOuting(Outing $enteredOuting): static
    {
        if (!$this->enteredOuting->contains($enteredOuting)) {
            $this->enteredOuting->add($enteredOuting);
        }

        return $this;
    }

    public function removeEnteredOuting(Outing $enteredOuting): static
    {
        $this->enteredOuting->removeElement($enteredOuting);

        return $this;
    }
}
