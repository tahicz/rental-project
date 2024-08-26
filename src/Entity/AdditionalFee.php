<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\AdditionalFeeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity(repositoryClass: AdditionalFeeRepository::class)]
#[ORM\Table(name: 'additional_fee')]
#[ORM\HasLifecycleCallbacks]
class AdditionalFee
{
    use Timestampable;

    #[ORM\Id]
    #[ORM\Column(type: 'ulid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.ulid_generator')]
    private ?Ulid $id = null;

    #[ORM\Column(length: 255, nullable: false)]
    private string $description;

    #[ORM\Column(nullable: false)]
    private int $feeAmount;

    #[ORM\Column(length: 255, nullable: false)]
    private string $paymentFrequency;

    #[ORM\Column(nullable: false)]
    private bool $billable;

    #[ORM\ManyToOne(inversedBy: 'additionalFees')]
    #[ORM\JoinColumn()]
    private ?RentalRecipe $rentRecipe = null;

    public function getId(): ?Ulid
    {
        return $this->id;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getFeeAmount(): int
    {
        return $this->feeAmount;
    }

    public function setFeeAmount(int $feeAmount): static
    {
        $this->feeAmount = $feeAmount;

        return $this;
    }

    public function getPaymentFrequency(): string
    {
        return $this->paymentFrequency;
    }

    public function setPaymentFrequency(string $paymentFrequency): static
    {
        $this->paymentFrequency = $paymentFrequency;

        return $this;
    }

    public function isBillable(): bool
    {
        return $this->billable;
    }

    public function setBillable(bool $billable): static
    {
        $this->billable = $billable;

        return $this;
    }

    public function getRentRecipe(): ?RentalRecipe
    {
        return $this->rentRecipe;
    }

    public function setRentRecipe(?RentalRecipe $rentRecipe): static
    {
        $this->rentRecipe = $rentRecipe;

        return $this;
    }
}
