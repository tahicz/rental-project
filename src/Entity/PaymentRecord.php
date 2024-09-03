<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\PaymentRecordRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity(repositoryClass: PaymentRecordRepository::class)]
#[ORM\Table(name: 'payment_record')]
#[ORM\HasLifecycleCallbacks]
class PaymentRecord
{
    use Timestampable;

    #[ORM\Id]
    #[ORM\Column(type: UlidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.ulid_generator')]
    private ?Ulid $id = null;

    #[ORM\ManyToOne(inversedBy: 'paymentRecords')]
    private ?Income $income = null;

    #[ORM\ManyToOne(inversedBy: 'paymentRecords')]
    private ?PaymentRecipe $paymentRecipe = null;

    #[ORM\Column(nullable: false)]
    private float $amount;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: false)]
    private \DateTimeImmutable $receivedOn;

    public function getId(): ?Ulid
    {
        return $this->id;
    }

    public function getIncome(): ?Income
    {
        return $this->income;
    }

    public function setIncome(?Income $income): static
    {
        $this->income = $income;

        return $this;
    }

    public function getPaymentRecipe(): ?PaymentRecipe
    {
        return $this->paymentRecipe;
    }

    public function setPaymentRecipe(?PaymentRecipe $paymentRecipe): static
    {
        $this->paymentRecipe = $paymentRecipe;

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

    public function getReceivedOn(): ?\DateTimeImmutable
    {
        return $this->receivedOn;
    }

    public function setReceivedOn(\DateTimeImmutable $receivedOn): static
    {
        $this->receivedOn = $receivedOn;

        return $this;
    }
}
