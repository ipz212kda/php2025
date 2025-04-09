<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 100)]
    private ?string $email = null;

    #[ORM\Column(length: 50)]
    private ?string $phone = null;

    /**
     * @var Collection<int, RideOrder>
     */
    #[ORM\OneToMany(targetEntity: RideOrder::class, mappedBy: 'client', orphanRemoval: true)]
    private Collection $rideOrders;

    public function __construct()
    {
        $this->rideOrders = new ArrayCollection();
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return Collection<int, RideOrder>
     */
    public function getRideOrders(): Collection
    {
        return $this->rideOrders;
    }

    public function addRideOrder(RideOrder $rideOrder): static
    {
        if (!$this->rideOrders->contains($rideOrder)) {
            $this->rideOrders->add($rideOrder);
            $rideOrder->setClient($this);
        }

        return $this;
    }

    public function removeRideOrder(RideOrder $rideOrder): static
    {
        if ($this->rideOrders->removeElement($rideOrder)) {
            // set the owning side to null (unless already changed)
            if ($rideOrder->getClient() === $this) {
                $rideOrder->setClient(null);
            }
        }

        return $this;
    }
}
