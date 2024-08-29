<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\OverpaymentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity(repositoryClass: OverpaymentRepository::class)]
#[ORM\Table(name: 'overpayment')]
#[ORM\HasLifecycleCallbacks]
class Overpayment
{
    use Timestampable;
    #[ORM\Id]
    #[ORM\Column(type: UlidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.ulid_generator')]
    private ?Ulid $id = null;

    #[ORM\Column(nullable: false, options: ['default' => 0.0])]
    private float $amount = 0.0;

    #[ORM\OneToOne(inversedBy: 'overpayment', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?PaymentRecord $paymentRecord = null;

    public function getId(): ?Ulid
    {
        return $this->id;
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

    public function getPaymentRecord(): ?PaymentRecord
    {
        return $this->paymentRecord;
    }

    public function setPaymentRecord(PaymentRecord $paymentRecord): static
    {
        $this->paymentRecord = $paymentRecord;

        return $this;
    }
}
