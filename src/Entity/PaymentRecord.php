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
    #[ORM\JoinColumn(nullable: false)]
    private ?PaymentRecipe $payment = null;
    #[ORM\JoinColumn(nullable: false, name: 'payment_id')]
    private ?PaymentRecipe $paymentRecipe = null;


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

    public function getPaymentRecipe(): ?PaymentRecipe
    {
        return $this->paymentRecipe;
    }

    public function setPaymentRecipe(?PaymentRecipe $paymentRecipe): static
    {
        $this->paymentRecipe = $paymentRecipe;

        return $this;
    }

    public function setPayment(?PaymentRecipe $payment): static
    {
        $this->payment = $payment;

        return $this;
    }

	public function __toString(): string
	{
		$nf = new \NumberFormatter('cs_CZ', \NumberFormatter::CURRENCY);

		return $nf->formatCurrency($this->getAmount(), 'CZK').' ('.$this->getPaymentDate()->format('d. m. Y').')';
	}
}
