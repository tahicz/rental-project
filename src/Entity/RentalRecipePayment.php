<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Helper\PaymentHelper;
use App\Repository\RentalRecipePaymentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RentalRecipePaymentRepository::class)]
#[ORM\Table(name: 'rental_recipe_payment')]
#[ORM\HasLifecycleCallbacks]
class RentalRecipePayment
{
    use Timestampable;

    #[ORM\Id]
    #[ORM\Column(type: 'ulid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.ulid_generator')]
    private ?Ulid $id = null;

    #[ORM\Column(nullable: false)]
    #[Assert\Positive()]
    private float $amount;

    #[ORM\Column(nullable: false)]
    #[Assert\Range(min: 1, max: 28)]
    private int $maturity;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: false)]
    private \DateTimeImmutable $validityFrom;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $validityTo = null;

    #[ORM\ManyToOne(inversedBy: 'payment')]
    #[ORM\JoinColumn(nullable: false)]
    private RentalRecipe $rentalRecipe;

    #[ORM\Column(type: Types::TEXT, nullable: false)]
    private string $note = '';

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

    public function getMaturity(): int
    {
        return $this->maturity;
    }

    public function getMaturityDate(): \DateTime
    {
        return PaymentHelper::createPaymentDate($this->validityFrom, $this->maturity);
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

    public function getValidityTo(): ?\DateTimeImmutable
    {
        return $this->validityTo;
    }

    public function setValidityTo(?\DateTimeImmutable $validityTo): static
    {
        $this->validityTo = $validityTo;

        return $this;
    }

    public function getRentalRecipe(): RentalRecipe
    {
        return $this->rentalRecipe;
    }

    public function setRentalRecipe(RentalRecipe $rentalRecipe): static
    {
        $this->rentalRecipe = $rentalRecipe;

        return $this;
    }

    public function getNote(): string
    {
        return $this->note;
    }

    public function setNote(string $note): static
    {
        $this->note = $note;

        return $this;
    }
}
