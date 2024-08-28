<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\PaymentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
#[ORM\Table(name: 'payment_recipe')]
#[ORM\HasLifecycleCallbacks]
class PaymentRecipe
{
    use Timestampable;

    #[ORM\Id]
    #[ORM\Column(type: UlidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.ulid_generator')]
    private ?Ulid $id = null;

    #[ORM\ManyToOne(inversedBy: 'payments')]
    #[ORM\JoinColumn()]
    private ?RentalRecipe $rentalRecipe = null;

    #[ORM\Column(nullable: false)]
    private float $payableAmount;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $paymentDate = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: false)]
    private \DateTimeImmutable $maturityDate;

    #[ORM\Column(nullable: true)]
    private ?float $paidAmount = null;

    public function getId(): ?Ulid
    {
        return $this->id;
    }

    public function getRentalRecipe(): ?RentalRecipe
    {
        return $this->rentalRecipe;
    }

    public function setRentalRecipe(?RentalRecipe $rentalRecipe): static
    {
        $this->rentalRecipe = $rentalRecipe;

        return $this;
    }

    public function getPayableAmount(): float
    {
        return $this->payableAmount;
    }

    public function setPayableAmount(float $payableAmount): static
    {
        $this->payableAmount = $payableAmount;

        return $this;
    }

    public function getPaymentDate(): ?\DateTimeImmutable
    {
        return $this->paymentDate;
    }

    public function setPaymentDate(\DateTimeImmutable $paymentDate): static
    {
        $this->paymentDate = $paymentDate;

        return $this;
    }

    public function getMaturityDate(): \DateTimeImmutable
    {
        return $this->maturityDate;
    }

    public function setMaturityDate(\DateTimeImmutable $maturityDate): static
    {
        $this->maturityDate = $maturityDate;

        return $this;
    }

    public function getPaidAmount(): ?float
    {
        return $this->paidAmount;
    }

    public function setPaidAmount(?float $paidAmount): static
    {
        $this->paidAmount = $paidAmount;

        return $this;
    }
}
