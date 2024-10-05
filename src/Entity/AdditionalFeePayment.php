<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Enum\SystemEnum;
use App\Exception\NonRemoveAbleEntity;
use App\Exception\NoValiditySetException;
use App\Helper\PaymentHelper;
use App\Repository\AdditionalFeePaymentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity(repositoryClass: AdditionalFeePaymentRepository::class)]
#[ORM\Table(name: 'additional_fee_payment')]
#[ORM\HasLifecycleCallbacks]
class AdditionalFeePayment
{
    use Timestampable;

    #[ORM\Id]
    #[ORM\Column(type: UlidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.ulid_generator')]
    private ?Ulid $id = null;

    #[ORM\ManyToOne(inversedBy: 'additionalFeePayments', cascade: ['persist'])]
    #[ORM\JoinColumn()]
    private ?AdditionalFee $additionalFee = null;

    #[ORM\Column(nullable: false)]
    private float $amount;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $validityFrom = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $validityTo = null;

    #[ORM\Column(type: Types::TEXT, nullable: false)]
    private string $note = '';

    public function getId(): ?Ulid
    {
        return $this->id;
    }

    public function getAdditionalFee(): ?AdditionalFee
    {
        return $this->additionalFee;
    }

    public function setAdditionalFee(?AdditionalFee $additionalFee): static
    {
        $this->additionalFee = $additionalFee;

        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getFormatedAmount(): string
    {
        return (string) PaymentHelper::getFormatedCurrency($this->amount, SystemEnum::CURRENCY->value);
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getValidityFrom(): ?\DateTimeImmutable
    {
        return $this->validityFrom;
    }

    public function setValidityFrom(?\DateTimeImmutable $validityFrom): static
    {
        $this->validityFrom = $validityFrom;

        return $this;
    }

    public function getValidityTo(): ?\DateTimeImmutable
    {
        return $this->validityTo;
    }

    public function setValidityTo(?\DateTimeImmutable $validityTo): static
    {
        $this->validityTo = $validityTo;

        return $this;
    }

    public function getNote(): string
    {
        return $this->note;
    }

    public function setNote(?string $note): static
    {
        if (null === $note) {
            $note = '';
        }
        $this->note = $note;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getFormatedAmount().' valid from '.$this->getValidityFrom()?->format('Y-m-d');
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setDefaultValidityFrom(): void
    {
        $allPayments = $this->getAdditionalFee()?->getAdditionalFeePayments();
        if (null === $allPayments) {
            throw new \RuntimeException('No additional fee exists');
        }
        $lastFeePayment = $allPayments->get($allPayments->count() - 2);

        if (null === $lastFeePayment || empty($this->getValidityFrom())) {
            $rentalRecipe = $this->getAdditionalFee()?->getRentRecipe();
            if (null === $rentalRecipe) {
                throw new \RuntimeException('No rental recipe set');
            }
            $this->setValidityFrom($rentalRecipe->getValidityFrom());
        } else {
            if (!$this->getValidityFrom() instanceof \DateTimeImmutable) {
                $this->setValidityFrom(new \DateTimeImmutable());
            } else {
                if ($this->getValidityFrom() < $lastFeePayment->getValidityFrom()) {
                    throw new NoValiditySetException($this->getId(), self::class);
                }
            }

            if ($lastFeePayment !== $this) {
                $lastFeePayment->setValidityTo($this->getValidityFrom()->modify('-1day'));
            }
        }
    }

    #[ORM\PreRemove]
    public function preRemove(): void
    {
        throw new NonRemoveAbleEntity($this);
    }
}
