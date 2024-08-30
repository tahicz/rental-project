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

    #[ORM\Column(nullable: false)]
    private float $amount;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: false)]
    private \DateTimeImmutable $paymentDate;

    #[ORM\ManyToOne(inversedBy: 'paymentRecords')]
    #[ORM\JoinColumn(name: 'payment_id', nullable: false)]
    private PaymentRecipe $paymentRecipe;

    #[ORM\OneToOne(mappedBy: 'paymentRecord', cascade: ['persist', 'remove'])]
    private ?Overpayment $overpayment = null;

    public function getId(): ?Ulid
    {
        return $this->id;
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

    public function getPaymentDate(): ?\DateTimeImmutable
    {
        return $this->paymentDate;
    }

    public function setPaymentDate(\DateTimeImmutable $paymentDate): static
    {
        $this->paymentDate = $paymentDate;

        return $this;
    }

    public function getPaymentRecipe(): PaymentRecipe
    {
        return $this->paymentRecipe;
    }

    public function setPaymentRecipe(PaymentRecipe $paymentRecipe): static
    {
        $this->paymentRecipe = $paymentRecipe;

        return $this;
    }

    public function getOverpayment(): ?Overpayment
    {
        return $this->overpayment;
    }

    public function setOverpayment(Overpayment $overpayment): static
    {
        // set the owning side of the relation if necessary
        if ($overpayment->getPaymentRecord() !== $this) {
            $overpayment->setPaymentRecord($this);
        }

        $this->overpayment = $overpayment;

        return $this;
    }

    public function __toString(): string
    {
        $nf = new \NumberFormatter('cs_CZ', \NumberFormatter::CURRENCY);
        $amount = $this->getAmount();
        if (null === $amount) {
            $amount = 0.0;
        }

        $paymentDate = $this->getPaymentDate();
        if (null === $paymentDate) {
            $paymentDate = new \DateTimeImmutable('now');
        }

        return $nf->formatCurrency($amount, 'CZK').' ('.$paymentDate->format('d. m. Y').')';
    }
}
