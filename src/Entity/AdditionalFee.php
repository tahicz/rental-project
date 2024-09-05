<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Enum\AdditionalFeeEnum;
use App\Enum\PaymentFrequencyEnum;
use App\Exception\NonRemoveAbleEntity;
use App\Repository\AdditionalFeeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Ulid;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[ORM\Entity(repositoryClass: AdditionalFeeRepository::class)]
#[ORM\Table(name: 'additional_fee')]
#[ORM\HasLifecycleCallbacks]
class AdditionalFee implements TranslatableInterface
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

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $validityFrom = null;

    #[ORM\OneToOne(targetEntity: self::class, cascade: ['persist', 'remove'])]
    private ?self $parent = null;

    #[ORM\OneToOne(targetEntity: self::class, cascade: ['persist', 'remove'])]
    private ?self $child = null;

    public function getId(): ?Ulid
    {
        return $this->id;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getTranslatableDescription(): string
    {
        return AdditionalFeeEnum::class.'.'.$this->description;
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

    public function getTranslatablePaymentFrequency(): string
    {
        return PaymentFrequencyEnum::class.'.'.$this->paymentFrequency;
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

    public function getValidityFrom(): ?\DateTimeImmutable
    {
        return $this->validityFrom;
    }

    public function setValidityFrom(\DateTimeImmutable $validityFrom): static
    {
        $this->validityFrom = $validityFrom;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getDescription();
    }

    public function __clone(): void
    {
        $this->id = new Ulid();
    }

    #[ORM\PrePersist]
    public function setDefaultValidityFrom(): void
    {
        if (null === $this->getValidityFrom()) {
            if ($this->rentRecipe instanceof RentalRecipe) {
                $date = $this->rentRecipe->getValidityFrom();
            } else {
                $date = new \DateTimeImmutable();
            }
            $this->setValidityFrom($date);
        }
    }

    #[ORM\PreRemove]
    public function preRemove(): void
    {
        throw new NonRemoveAbleEntity($this);
    }

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return AdditionalFeeEnum::getTranslateAbleValue($this->getDescription())->trans($translator, $locale);
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    public function getChild(): ?self
    {
        return $this->child;
    }

    public function setChild(?self $child): static
    {
        $this->child = $child;

        return $this;
    }
}
