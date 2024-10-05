<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Enum\AdditionalFeeEnum;
use App\Enum\PaymentFrequencyEnum;
use App\Exception\NonRemoveAbleEntity;
use App\Repository\AdditionalFeeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\Column(length: 255, nullable: false)]
    private string $paymentFrequency;

    #[ORM\Column(nullable: false)]
    private bool $billable;

    #[ORM\ManyToOne(inversedBy: 'additionalFees')]
    #[ORM\JoinColumn()]
    private ?RentalRecipe $rentRecipe = null;

    /**
     * @var Collection<int, AdditionalFeePayment>
     */
    #[ORM\OneToMany(targetEntity: AdditionalFeePayment::class, mappedBy: 'additionalFee', cascade: ['persist'])]
    #[ORM\OrderBy(['validityFrom' => 'ASC'])]
    private Collection $additionalFeePayments;

    public function __construct()
    {
        $this->additionalFeePayments = new ArrayCollection();
    }

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

    public function __toString(): string
    {
        return $this->getDescription();
    }

    public function __clone(): void
    {
        $this->id = new Ulid();
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

    /**
     * @return Collection<int, AdditionalFeePayment>
     */
    public function getAdditionalFeePayments(): Collection
    {
        return $this->additionalFeePayments;
    }

    public function addAdditionalFeePayment(AdditionalFeePayment $additionalFeePayment): static
    {
        if (!$this->additionalFeePayments->contains($additionalFeePayment)) {
            $this->additionalFeePayments->add($additionalFeePayment);
            $additionalFeePayment->setAdditionalFee($this);
        }

        return $this;
    }

    public function removeAdditionalFeePayment(AdditionalFeePayment $additionalFeePayment): static
    {
        if ($this->additionalFeePayments->removeElement($additionalFeePayment)) {
            // set the owning side to null (unless already changed)
            if ($additionalFeePayment->getAdditionalFee() === $this) {
                $additionalFeePayment->setAdditionalFee(null);
            }
        }

        return $this;
    }

    public function getFeePaymentForDate(\DateTimeImmutable $date): AdditionalFeePayment|false
    {
        return $this->getAdditionalFeePayments()
            ->filter(function (AdditionalFeePayment $payment) use ($date) {
                return $payment->getValidityFrom() <= $date && (null === $payment->getValidityTo() || $payment->getValidityTo() > $date);
            })
            ->first();
    }
}
