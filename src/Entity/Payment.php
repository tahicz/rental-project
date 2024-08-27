<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\PaymentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
#[ORM\Table(name: 'payment')]
#[ORM\HasLifecycleCallbacks]
class Payment
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
    private float $amount;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $paymentDate = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: false)]
    private \DateTimeImmutable $maturityDate;

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

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

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
}
