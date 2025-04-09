<?php

namespace App\Entity;

use App\Repository\RouteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RouteRepository::class)]
class Route
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $start_location = null;

    #[ORM\Column(length: 255)]
    private ?string $end_location = null;

    #[ORM\Column]
    private ?float $distance_km = null;

    /**
     * @var Collection<int, RideOrder>
     */
    #[ORM\OneToMany(targetEntity: RideOrder::class, mappedBy: 'route')]
    private Collection $rideOrders;

    public function __construct()
    {
        $this->rideOrders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartLocation(): ?string
    {
        return $this->start_location;
    }

    public function setStartLocation(string $start_location): static
    {
        $this->start_location = $start_location;

        return $this;
    }

    public function getEndLocation(): ?string
    {
        return $this->end_location;
    }

    public function setEndLocation(string $end_location): static
    {
        $this->end_location = $end_location;

        return $this;
    }

    public function getDistanceKm(): ?float
    {
        return $this->distance_km;
    }

    public function setDistanceKm(float $distance_km): static
    {
        $this->distance_km = $distance_km;

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
            $rideOrder->setRoute($this);
        }

        return $this;
    }

    public function removeRideOrder(RideOrder $rideOrder): static
    {
        if ($this->rideOrders->removeElement($rideOrder)) {
            // set the owning side to null (unless already changed)
            if ($rideOrder->getRoute() === $this) {
                $rideOrder->setRoute(null);
            }
        }

        return $this;
    }
}
