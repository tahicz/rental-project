<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Enum\PaymentFrequencyEnum;
use App\Repository\RentalRecipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity(repositoryClass: RentalRecipeRepository::class)]
#[ORM\Table(name: 'rental_recipe')]
#[ORM\HasLifecycleCallbacks]
class RentalRecipe
{
    use Timestampable;

    #[ORM\Id]
    #[ORM\Column(type: 'ulid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.ulid_generator')]
    private ?Ulid $id = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: false)]
    private \DateTimeImmutable $validityFrom;

    /**
     * @var Collection<int, AdditionalFee>
     */
    #[ORM\OneToMany(targetEntity: AdditionalFee::class, mappedBy: 'rentRecipe', cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['description' => 'ASC', 'validityFrom' => 'ASC'])]
    private Collection $additionalFees;

    /**
     * @var Collection<int, PaymentRecipe>
     */
    #[ORM\OneToMany(targetEntity: PaymentRecipe::class, mappedBy: 'rentalRecipe')]
    private Collection $paymentsPlan;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $validityTo = null;

    /**
     * @var Collection<int, RentalRecipePayment>
     */
    #[ORM\OneToMany(targetEntity: RentalRecipePayment::class, mappedBy: 'rentalRecipe')]
    #[ORM\OrderBy(['validityFrom' => 'ASC'])]
    private Collection $recipePayment;

    public function __construct()
    {
        $this->additionalFees = new ArrayCollection();
        $this->paymentsPlan = new ArrayCollection();
        $this->recipePayment = new ArrayCollection();
    }

    public function getId(): ?Ulid
    {
        return $this->id;
    }

    public function getFullRateForCurrentMonth(): float
    {
        $today = new \DateTimeImmutable();

        return $this->getFullPaymentForMonth($today);
    }

    public function getFullPaymentForMonth(\DateTimeImmutable $paymentDate): float
    {
        $paymentsForRecipe = $this->getRecipePayment()->filter(function (RentalRecipePayment $payment) use ($paymentDate) {
            return $payment->getValidityFrom() >= $paymentDate && (null === $payment->getValidityTo() || $payment->getValidityTo() < $paymentDate);
        });

        $payment = $paymentsForRecipe->first();
        if (false === $payment) {
            $amount = 0.0;
        } else {
            $amount = $payment->getAmount();
        }

        foreach ($this->getAdditionalFees() as $additionalFee) {
            if ($additionalFee->getValidityFrom() <= $paymentDate && ($additionalFee->getValidityTo() > $paymentDate || null === $additionalFee->getValidityTo())) {
                $payment = match ($additionalFee->getPaymentFrequency()) {
                    PaymentFrequencyEnum::ANNUALLY->value => $additionalFee->getFeeAmount() / 12,
                    default => $additionalFee->getFeeAmount(),
                };

                $amount += $payment;
            }
        }

        return round($amount, 2);
    }

    public function getValidityFrom(): \DateTimeImmutable
    {
        return $this->validityFrom;
    }

    public function setValidityFrom(\DateTimeImmutable $validityFrom): static
    {
        $this->validityFrom = $validityFrom;

        return $this;
    }

    /**
     * @return Collection<int, AdditionalFee>
     */
    public function getAdditionalFees(): Collection
    {
        return $this->additionalFees;
    }

    public function addAdditionalFee(AdditionalFee $additionalFee): static
    {
        if (!$this->additionalFees->contains($additionalFee)) {
            $this->additionalFees->add($additionalFee);
            $additionalFee->setRentRecipe($this);
        }

        return $this;
    }

    public function removeAdditionalFee(AdditionalFee $additionalFee): static
    {
        if ($this->additionalFees->removeElement($additionalFee)) {
            // set the owning side to null (unless already changed)
            if ($additionalFee->getRentRecipe() === $this) {
                $additionalFee->setRentRecipe(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PaymentRecipe>
     */
    public function getPaymentsPlan(): Collection
    {
        return $this->paymentsPlan;
    }

    public function addPaymentPlan(PaymentRecipe $payment): static
    {
        if (!$this->paymentsPlan->contains($payment)) {
            $this->paymentsPlan->add($payment);
            $payment->setRentalRecipe($this);
        }

        return $this;
    }

    public function removePaymentPlan(PaymentRecipe $payment): static
    {
        if ($this->paymentsPlan->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getRentalRecipe() === $this) {
                $payment->setRentalRecipe(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->id;
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

    /**
     * @return Collection<int, RentalRecipePayment>
     */
    public function getRecipePayment(): Collection
    {
        return $this->recipePayment;
    }
}
