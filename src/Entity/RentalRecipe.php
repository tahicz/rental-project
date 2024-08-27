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
use Symfony\Component\Validator\Constraints as Assert;

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

    #[ORM\Column(nullable: false)]
    #[Assert\Positive()]
    private float $basicRent;

    #[ORM\Column(nullable: false)]
    #[Assert\Range(min: 1, max: 28)]
    private int $maturity;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: false)]
    private \DateTimeImmutable $validityFrom;

    /**
     * @var Collection<int, AdditionalFee>
     */
    #[ORM\OneToMany(targetEntity: AdditionalFee::class, mappedBy: 'rentRecipe', cascade: ['persist'], orphanRemoval: true)]
    private Collection $additionalFees;

    /**
     * @var Collection<int, Payment>
     */
    #[ORM\OneToMany(targetEntity: Payment::class, mappedBy: 'rentalRecipe')]
    private Collection $payments;

    public function __construct()
    {
        $this->additionalFees = new ArrayCollection();
        $this->payments = new ArrayCollection();
    }

    public function getId(): ?Ulid
    {
        return $this->id;
    }

    public function getBasicRent(): float
    {
        return $this->basicRent;
    }

    public function setBasicRent(float $basicRent): static
    {
        $this->basicRent = $basicRent;

        return $this;
    }

    public function getFullMonthlyRate(): float
    {
        $monthlyRate = $this->getBasicRent();
        foreach ($this->getAdditionalFees() as $additionalFee) {
            $payment = match ($additionalFee->getPaymentFrequency()) {
                PaymentFrequencyEnum::ANNUALLY->value => $additionalFee->getFeeAmount() / 12,
                default => $additionalFee->getFeeAmount(),
            };
            $monthlyRate += $payment;
        }

        return $monthlyRate;
    }

    public function getMaturity(): ?int
    {
        return $this->maturity;
    }

    public function setMaturity(int $maturity): static
    {
        $this->maturity = $maturity;

        return $this;
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
     * @return Collection<int, Payment>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): static
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->setRentalRecipe($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): static
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getRentalRecipe() === $this) {
                $payment->setRentalRecipe(null);
            }
        }

        return $this;
    }
}
