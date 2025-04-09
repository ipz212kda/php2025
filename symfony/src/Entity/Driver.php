<?php

namespace App\Entity;

use App\Repository\DriverRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DriverRepository::class)]
class Driver
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 100)]
    private ?string $car_model = null;

    #[ORM\Column(length: 255)]
    private ?string $license_plate = null;

    #[ORM\Column(length: 50)]
    private ?string $phone = null;

    /**
     * @var Collection<int, RideOrder>
     */
    #[ORM\OneToMany(targetEntity: RideOrder::class, mappedBy: 'driver')]
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

    public function getCarModel(): ?string
    {
        return $this->car_model;
    }

    public function setCarModel(string $car_model): static
    {
        $this->car_model = $car_model;

        return $this;
    }

    public function getLicensePlate(): ?string
    {
        return $this->license_plate;
    }

    public function setLicensePlate(string $license_plate): static
    {
        $this->license_plate = $license_plate;

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
            $rideOrder->setDriver($this);
        }

        return $this;
    }

    public function removeRideOrder(RideOrder $rideOrder): static
    {
        if ($this->rideOrders->removeElement($rideOrder)) {
            // set the owning side to null (unless already changed)
            if ($rideOrder->getDriver() === $this) {
                $rideOrder->setDriver(null);
            }
        }

        return $this;
    }
}
