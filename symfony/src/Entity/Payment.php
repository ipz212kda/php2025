<?php

namespace App\Entity;

use App\Repository\PaymentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
class Payment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'payment', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?RideOrder $rideOrder = null;

    #[ORM\Column]
    private ?float $amount = null;

    #[ORM\Column(length: 100)]
    private ?string $payment_method = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $paid_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRideOrder(): ?RideOrder
    {
        return $this->rideOrder;
    }

    public function setRideOrder(RideOrder $rideOrder): static
    {
        $this->rideOrder = $rideOrder;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getPaymentMethod(): ?string
    {
        return $this->payment_method;
    }

    public function setPaymentMethod(string $payment_method): static
    {
        $this->payment_method = $payment_method;

        return $this;
    }

    public function getPaidAt(): ?\DateTimeInterface
    {
        return $this->paid_at;
    }

    public function setPaidAt(\DateTimeInterface $paid_at): static
    {
        $this->paid_at = $paid_at;

        return $this;
    }
}
